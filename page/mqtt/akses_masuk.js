const mqtt = require('mqtt');
const axios = require('axios');

// Konfigurasi MQTT
const broker = 'mqtt://broker.emqx.io';
const topicMasuk = 'parkir/akses_masuk';
const topicBalasan = 'parkir/akses_balasan';

// Connect ke broker MQTT
const client = mqtt.connect(broker);

client.on('connect', () => {
    console.log('Terhubung ke broker MQTT');

    // Subscribe ke topic untuk menerima UID dari ESP32
    client.subscribe(topicMasuk, (err) => {
        if (!err) {
            console.log(`Subscribed to topic: ${topicMasuk}`);
        } else {
            console.error('Gagal subscribe:', err);
        }
    });
});

// Saat menerima pesan dari ESP32
client.on('message', async (topic, message) => {
    if (topic === topicMasuk) {
        console.log('Pesan diterima:', message.toString());

        try {
            const jsonData = JSON.parse(message.toString());
            const uid = jsonData.kartu;

            console.log('UID diterima:', uid);

            // Kirim UID ke PHP untuk validasi
            const url = `http://localhost/skripsi/Pengcodean/page/entry_masuk.php?uid=${uid}`;
            console.log('Mengirim request ke:', url);

            const response = await axios.get(url);
            const hasil = response.data;

            console.log('Respon dari server PHP:', hasil);

            // Cek hasil dari PHP dan balas ke ESP32
            if (hasil.status === 'success') {
                console.log('Akses diterima untuk UID:', JSON.stringify(hasil));
                client.publish(topicBalasan, JSON.stringify({
                    akses: "masuk",
                    nama: hasil.nama,
                    uid: hasil.uid,
                    jam_masuk: hasil.jam_masuk
                }));
            } else if (hasil.status === 'ditolak') {
                console.log('Akses ditolak untuk UID:', JSON.stringify(hasil));
                client.publish(topicBalasan, JSON.stringify({
                    akses: "masuk",
                    status: "Ditolak",
                    message: "Saldo tidak mencukupi"
                }));
            } else {
                client.publish(topicBalasan, JSON.stringify({ status: "Ditolak" })); // Balasan ke ESP32
            }

        } catch (error) {
            console.error('Error saat memproses pesan atau request:', error.message);
        }
    }
});
