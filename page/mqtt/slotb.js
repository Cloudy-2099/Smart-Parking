const mqtt = require('mqtt');
const axios = require('axios');

// Konfigurasi MQTT
const broker = 'mqtt://broker.emqx.io';
const topicRFID = 'parkir/status/slotb';
const baseFeedbackTopic = 'parkir/feedback/slotb'; // Dasar topik feedback

// Koneksi ke MQTT Broker
const client = mqtt.connect(broker);

client.on('connect', () => {
    console.log('✅ Terhubung ke broker MQTT');
    client.subscribe(topicRFID, (err) => {
        if (!err) {
            console.log(`✅ Subscribed to topic: ${topicRFID}`);
        } else {
            console.error('❌ Gagal subscribe:', err);
        }
    });
});

client.on('message', async (receivedTopic, message) => {
    if (receivedTopic === topicRFID) {
        try {
            let data = JSON.parse(message.toString());
            console.log('📩 Data diterima dari ESP32:', data);

            const response = await axios.post('http://localhost/skripsi/Pengcodean/page/update_location.php', data, {
                headers: { 'Content-Type': 'application/json' }
            });

            console.log('📨 Response dari PHP:', response.data);


            if (response.data.status === 'success') {
                client.publish(baseFeedbackTopic, JSON.stringify({
                    status: "Diterima",
                    uid: response.data.uid,
                    plat_nomor: response.data.plat_nomor,
                    lokasi: response.data.lokasi,
                    nilai: response.data.nilai
                }), { retain: true });  // Pesan retained per slot
            } else {
                client.publish(baseFeedbackTopic, JSON.stringify({
                    status: "Ditolak",
                    message: response.data.message,
                    lokasi: response.data.lokasi
                }), { retain: false }); // Bisa juga true jika ingin disimpan
            }
        } catch (error) {
            console.error('❌ Error saat request ke PHP:', error.message);
        }
    }
});

client.on('error', (err) => console.error('❌ MQTT error:', err));
