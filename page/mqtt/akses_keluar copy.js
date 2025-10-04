const mqtt = require('mqtt');
const axios = require('axios');

// Konfigurasi MQTT
const broker = 'mqtt://broker.emqx.io';
const topicKeluar = 'parkir/akses_keluar';
const topicBalasan = 'parkir/akses_balasan';
const topicA = 'parkir/feedback/slotb'; // Dasar topik feedback
const topicB = 'parkir/feedback/slota'; // Dasar topik feedback

// Connect ke broker MQTT
const client = mqtt.connect(broker);

client.on('connect', () => {
    console.log('Terhubung ke broker MQTT');

    // Subscribe ke topic untuk menerima UID dari ESP32
    client.subscribe(topicKeluar, (err) => {
        if (!err) {
            console.log(`Subscribed to topic: ${topicKeluar}`);
        } else {
            console.error('Gagal subscribe:', err);
        }
    });
});

// Saat menerima pesan dari ESP32
client.on('message', async (topic, message) => {
    if (topic === topicKeluar) {
        console.log('Pesan diterima:', message.toString());

        try {
            const jsonData = JSON.parse(message.toString());
            const uid = jsonData.kartu;

            console.log('UID diterima:', uid);

            // Kirim UID ke PHP untuk validasi
            const url = `http://localhost/skripsi/Pengcodean/page/entry_keluar.php?uid=${uid}`;
            console.log('Mengirim request ke:', url);

            const response = await axios.get(url);
            const hasil = response.data;

            console.log('Respon dari server PHP:', hasil);

            // Cek hasil dari PHP dan balas ke ESP32
            if (hasil.status === 'Diterima') {
                console.log('Akses diterima untuk UID:', JSON.stringify(hasil));
                client.publish(topicBalasan, JSON.stringify({
                    status: "Diterima",
                    uid: hasil.uid,
                    jam_keluar: hasil.jam_keluar
                }));
                if (hasil.lokasi === 'slotb' && hasil.nilai === '0') {
                    client.publish(topicB, JSON.stringify({
                        status: "Diterima",
                        uid: hasil.uid,
                        plat_nomor: hasil.plat_nomor,
                        lokasi: hasil.lokasi,
                        nilai: hasil.nilai
                    }), { retain: true });  // Pesan retained per slot
                } else if (hasil.lokasi === 'slota' && hasil.nilai === '0') {
                    client.publish(topicA, JSON.stringify({
                        status: "Diterima",
                        uid: hasil.uid,
                        plat_nomor: hasil.plat_nomor,
                        lokasi: hasil.lokasi,
                        nilai: hasil.nilai
                    }), { retain: true });  // Pesan retained per slot
                } else {
                    client.publish(topicBalasan, JSON.stringify({ status: "Ditolak" })); // Balasan ke ESP32
                }
            } else {
                client.publish(topicBalasan, JSON.stringify({ status: "Ditolak" })); // Balasan ke ESP32
            }
        } catch (error) {
            console.error('Error saat memproses pesan atau request:', error.message);
        }
    }
});
