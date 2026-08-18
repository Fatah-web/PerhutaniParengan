document.addEventListener("DOMContentLoaded", function () {

    if (typeof dashboardData === "undefined") return;

    var tanamanChart = {
        series: [
            dashboardData.pohon_hidup,
            dashboardData.pohon_mati,
            dashboardData.jumlah_tanaman
        ],
        labels: ["Pohon Hidup", "Pohon Mati", "Tanaman"],
        chart: {
            height: 220,
            type: "donut",
            fontFamily: "Plus Jakarta Sans, sans-serif",
            foreColor: "#c6d1e9"
        },
        colors: ["#00E396", "#FF4560", "#775DD0"],
        tooltip: {
            theme: "dark", 
            style: {
                fontSize: "15px"
            },
            y: {
                formatter: v => v.toLocaleString("id-ID") + " Pohon"
            }
        },
        dataLabels: { enabled: false },
        legend: { show: false },
        stroke: { show: false },
        plotOptions: {
            pie: {
                donut: {
                    size: "70%",
                    labels: {
                        show: true, 
                         name: {
                            show: true,
                            fontSize: "19px", // BESARIN TEKS TENGAH
                            fontWeight: 600,
                            offsetY: 8
                        },
                        value: {
                            show: false,
                            fontSize: "5px",
                            formatter: v => v.toLocaleString("id-ID") + " Pohon"
                        }
                    }
                }
            }
        }
    };

    new ApexCharts(document.querySelector("#chartTanaman"), tanamanChart).render();


    // ICON PERSENTASE
    const iconEl = document.querySelector("#tanamanIconi");
    const valueEl = document.querySelector("#tanamanValue");

    const jumlahTanaman = dashboardData.jumlah_tanaman;
    const pohonHidup = dashboardData.pohon_hidup;

    let persentase = jumlahTanaman ? (pohonHidup / jumlahTanaman) * 100 : 0;

    valueEl.textContent = persentase.toFixed(2) + "%";

    if (persentase >= 50) {
        iconEl.className = "ti ti-arrow-up-left text-success";
    } else {
        iconEl.className = "ti ti-arrow-down-right text-danger";
    }

});