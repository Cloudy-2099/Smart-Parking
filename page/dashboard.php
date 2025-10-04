<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Parkir</title>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <style>
        .sensor-status {
            position: relative;
            display: inline-block;
            text-align: center;
            width: 200px;
            margin: 10px;
        }

        .sensor-image {
            width: 100px;
            height: 100px;
            transition: opacity 0.5s ease;
            opacity: 0.3;
        }

        .maintenance-text {
            color: red;
            font-weight: bold;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            position: absolute;
            top: 10px;
            left: 0;
            right: 0;
        }

        .maintenance-mode {
            opacity: 0.3 !important;
        }

        .btn-maintenance {
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-maintenance:hover {
            background-color: #d32f2f;
        }

        .uid-text {
            margin-top: 5px;
            font-size: 14px;
            color: #333;
            font-weight: bold;
        }

        .summary {
            margin: 20px;
            font-size: 18px;
        }

        .summary span {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="content-wrapper">

        <div class="content-header">
            <h1>Dashboard Parkir</h1>
        </div>

        <div class="summary">
            Total Slot: <span id="totalSlot">0</span> |
            Terisi: <span id="slotTerisi">0</span> |
            Kosong: <span id="slotKosong">0</span>
        </div>

        <div class="content">
            <h2>Data Sensor</h2>
            <div id="sensorData">
                <div id="slota" class="sensor-status">
                    <img id="image_slota" class="sensor-image" src="car.png" alt="Sensor slota">
                    <p>Slot A</p>
                    <button class="btn-maintenance" id="btn_slota">Toggle Maintenance</button>
                    <p id="maintenance_slota" class="maintenance-text">Maintenance Mode</p>
                    <p id="uid_slota" class="uid-text"></p>
                    <p id="plat_slota" class="uid-text"></p>
                </div>
                <div id="slotb" class="sensor-status">
                    <img id="image_slotb" class="sensor-image" src="car.png" alt="Sensor slotb">
                    <p>Slot B</p>
                    <button class="btn-maintenance" id="btn_slotb">Toggle Maintenance</button>
                    <p id="maintenance_slotb" class="maintenance-text">Maintenance Mode</p>
                    <p id="uid_slotb" class="uid-text"></p>
                    <p id="plat_slotb" class="uid-text"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const client = mqtt.connect('wss://broker.emqx.io:8084/mqtt');
        const topicFeedbackPrefix = 'parkir/feedback/';
        const lokasiValid = ["slota", "slotb"];
        let maintenanceStatus = {};
        let slotStatus = {
            slota: false,
            slotb: false
        };

        function updateSummary() {
            const total = lokasiValid.length;
            let terisi = 0;

            lokasiValid.forEach(slot => {
                if (slotStatus[slot]) terisi++;
            });

            const kosong = total - terisi;

            document.getElementById("totalSlot").textContent = total;
            document.getElementById("slotTerisi").textContent = terisi;
            document.getElementById("slotKosong").textContent = kosong;

            const rekapData = {
                total,
                terisi,
                kosong
            };

            client.publish("parkir/rekap", JSON.stringify(rekapData), {
                retain: true
            });
            console.log("📢 Rekap dikirim ke ESP32 lain:", rekapData);
        }

        function updateSlot(lokasi, uid, plat, status, nilai) {
            if (!lokasiValid.includes(lokasi) && lokasi !== "") {
                console.warn(`⚠️ Lokasi tidak valid (${lokasi}), mengabaikan update.`);
                return;
            }

            if (maintenanceStatus[lokasi]) {
                console.warn(`⛔ ${lokasi} dalam mode Maintenance, update diabaikan.`);
                return;
            }

            lokasiValid.forEach(slot => {
                if (slot !== lokasi) {
                    const uidElement = document.getElementById(`uid_${slot}`);
                    const platElement = document.getElementById(`plat_${slot}`);
                    const imageElement = document.getElementById(`image_${slot}`);

                    if (uidElement && uidElement.textContent.includes(uid)) {
                        uidElement.textContent = "";
                        platElement.textContent = "";
                        imageElement.style.opacity = "0.3"; // Reset opacity
                        slotStatus[slot] = false;
                    }
                }
            });

            if (lokasi !== "") {
                const image = document.getElementById(`image_${lokasi}`);
                const uidElement = document.getElementById(`uid_${lokasi}`);
                const platElement = document.getElementById(`plat_${lokasi}`);

                if (!image || !uidElement || !platElement) return;

                // Only show UID and Plat if the status is 'Diterima' and nilai is 1
                if (nilai === "1" && status === "Diterima") {
                    uidElement.textContent = `UID: ${uid}`;
                    platElement.textContent = `PLAT: ${plat}`;
                    image.style.opacity = "1.0"; // Full opacity if accepted
                    slotStatus[lokasi] = true;
                } else {
                    // Hide UID and Plat if not valid
                    uidElement.textContent = "";
                    platElement.textContent = "";
                    image.style.opacity = "0.3"; // Reduced opacity if not valid
                    slotStatus[lokasi] = false;
                }
            } else {
                const image = document.getElementById(`image_${lokasi}`);
                if (image) image.style.opacity = "0.3";
                slotStatus[lokasi] = false;
            }
            updateSummary();
        }




        client.on('connect', () => {
            console.log('✅ MQTT Connected!');
            lokasiValid.forEach(slot => {
                client.subscribe(`parkir/feedback/${slot}`);
                client.subscribe(`parkir/maintenance/${slot}`);
            });
        });

        client.on('message', (topic, message) => {
            let data;
            try {
                data = JSON.parse(message.toString());
            } catch (e) {
                console.error('❌ JSON Parse error:', e);
                return;
            }

            if (topic.startsWith("parkir/feedback/")) {
                const {
                    lokasi,
                    uid,
                    plat_nomor: plat,
                    status,
                    nilai
                } = data;
                updateSlot(lokasi, uid, plat, status, nilai); // Kirim nilai sebagai parameter
            }

            if (topic.startsWith("parkir/maintenance/")) {
                const lokasi = topic.split("/")[2];
                const isActive = data.status === "ON";
                toggleMaintenanceUI(lokasi, isActive);
            }
        });


        fetch('/skripsi/Pengcodean/page/get_status.php')
            .then(res => res.json())
            .then(data => {
                for (const lokasi in data) {
                    const slotVal = data[lokasi].slot;
                    const uid = data[lokasi].uid || "";
                    const plat = data[lokasi].plat || "";
                    const status = data[lokasi].status;
                    const nilai = data[lokasi].nilai; // Get nilai field

                    if (data[lokasi].status === "Masuk" || nilai === "1") {
                        updateSlot(lokasi, uid, plat, "Diterima", nilai);
                    } else {
                        updateSlot(lokasi, "", "", "", nilai); // Clear slot if not valid
                    }

                    if (data[lokasi].maintenance === "ON") {
                        toggleMaintenanceUI(lokasi, true);
                    }
                }
                updateSummary();
            })
            .catch(err => console.error("❌ Gagal mengambil data:", err));



        function toggleMaintenance(lokasi) {
            const newStatus = !maintenanceStatus[lokasi];
            maintenanceStatus[lokasi] = newStatus;
            toggleMaintenanceUI(lokasi, newStatus);

            const topicSlot = `parkir/maintenance/${lokasi}`;
            const maintenanceData = {
                lokasi,
                status: newStatus ? "ON" : "OFF"
            };
            client.publish(topicSlot, JSON.stringify(maintenanceData), {
                retain: true
            });
        }

        function toggleMaintenanceUI(lokasi, isActive) {
            const image = document.getElementById(`image_${lokasi}`);
            const maintenanceText = document.getElementById(`maintenance_${lokasi}`);
            const button = document.getElementById(`btn_${lokasi}`);
            const uidElement = document.getElementById(`uid_${lokasi}`);
            const platElement = document.getElementById(`plat_${lokasi}`);

            if (isActive) {
                image.classList.add("maintenance-mode");
                maintenanceText.style.opacity = "1";
                button.textContent = "Disable Maintenance";
                uidElement.style.display = "none";
                platElement.style.display = "none";
            } else {
                image.classList.remove("maintenance-mode");
                maintenanceText.style.opacity = "0";
                button.textContent = "Enable Maintenance";
                uidElement.style.display = "block";
                platElement.style.display = "block";
            }

            maintenanceStatus[lokasi] = isActive;
        }

        lokasiValid.forEach(lokasi => {
            const button = document.getElementById(`btn_${lokasi}`);
            if (button) {
                button.addEventListener("click", () => toggleMaintenance(lokasi));
            }
        });
    </script>
</body>

</html>