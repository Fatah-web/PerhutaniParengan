document.addEventListener("DOMContentLoaded", function () {

    if (typeof dashboardData === "undefined") return;

    // ==========================================
    // DATA
    // ==========================================
    const target =
        Number(dashboardData.target) || 0;

    const realisasiTanam =
        Number(dashboardData.realisasi_tanam) || 0;

    const gap =
        Math.max(0, target - realisasiTanam);


    // ==========================================
    // DATA CHART
    // ==========================================
    const seriesData = [
        target,
        realisasiTanam,
        gap
    ];

    const labelsData = [
        "Target",
        "Realisasi Tanam",
        "Gap"
    ];

    const colorsData = [
        "#775DD0",
        "#00E396",
        "#FF4560"
    ];


    // ==========================================
    // ELEMENT CHART
    // ==========================================
    const chartElement =
        document.querySelector("#chartTanamanGAP");

    if (!chartElement) return;


    // ==========================================
    // TAMBAHKAN HTML TENGAH
    // ==========================================
    chartElement.style.position = "relative";

    const centerText =
        document.createElement("div");

    centerText.id =
        "chartTanamanGAPCenter";

    centerText.style.position = "absolute";
    centerText.style.top = "50%";
    centerText.style.left = "50%";
    centerText.style.transform =
        "translate(-50%, -50%)";

    centerText.style.textAlign = "center";
    centerText.style.pointerEvents = "none";
    centerText.style.zIndex = "10";
    centerText.style.lineHeight = "1.2";

    centerText.innerHTML = `

        <div
            id="centerChartValue"
            style="
                font-size:24px;
                font-weight:700;
                color:${colorsData[0]};
                white-space:nowrap;
            "
        >
            ${target.toLocaleString("id-ID")}
        </div>

        <div
            id="centerChartLabel"
            style="
                margin-top:4px;
                font-size:14px;
                font-weight:600;
                color:${colorsData[0]};
                white-space:nowrap;
            "
        >
            Target
        </div>

    `;

    chartElement.appendChild(centerText);


    // ==========================================
    // CHART
    // ==========================================
    const tanamanChart = {

        series: seriesData,

        labels: labelsData,

        chart: {

            height: 220,

            type: "donut",

            fontFamily:
                "Plus Jakarta Sans, sans-serif",

            foreColor: "#c6d1e9",
            // ======================================
            // ANIMASI CHART
            // ======================================
            animations: {
                enabled: true,
                easing: "easeinout",
                speed: 1400,
                animateGradually: {
                    enabled: true,
                    delay: 200
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 600
                }
            },


            // ======================================
            // EVENT HOVER
            // ======================================
            events: {

                dataPointMouseEnter:
                    function (
                        event,
                        chartContext,
                        config
                    ) {

                        const index =
                            config.dataPointIndex;

                        updateCenterText(index);

                    },

                dataPointMouseLeave:
                    function () {

                        // Kembali ke Target
                        updateCenterText(0);

                    },

                dataPointSelection:
                    function (
                        event,
                        chartContext,
                        config
                    ) {

                        const index =
                            config.dataPointIndex;

                        updateCenterText(index);

                    }

            }

        },


        // ==========================================
        // WARNA
        // ==========================================
        colors: colorsData,


        // ==========================================
        // TOOLTIP
        // ==========================================
        tooltip: {

            theme: "dark",

            style: {
                fontSize: "15px"
            },

            y: {

                formatter:
                    function (v) {

                        return Number(v)
                            .toLocaleString("id-ID")
                            + " Pohon";

                    }

            }

        },


        // ==========================================
        // DATA LABEL
        // ==========================================
        dataLabels: {
            enabled: false
        },


        // ==========================================
        // LEGEND
        // ==========================================
        legend: {
            show: false
        },


        // ==========================================
        // STROKE
        // ==========================================
        stroke: {
            show: false
        },


        // ==========================================
        // DONUT
        // ==========================================
        plotOptions: {

            pie: {

                donut: {

                    size: "70%",

                    labels: {

                        show: false

                    }

                }

            }

        }

    };


    // ==========================================
    // UPDATE TENGAH
    // ==========================================
    function updateCenterText(index) {

        const valueEl =
            document.querySelector(
                "#centerChartValue"
            );

        const labelEl =
            document.querySelector(
                "#centerChartLabel"
            );


        if (!valueEl || !labelEl) return;


        const value =
            seriesData[index] || 0;

        const label =
            labelsData[index];

        const color =
            colorsData[index];


        // ANGKA
        valueEl.textContent =
            Number(value)
                .toLocaleString("id-ID");


        // KETERANGAN
        labelEl.textContent =
            label;


        // WARNA ANGKA
        valueEl.style.color =
            color;


        // WARNA KETERANGAN
        labelEl.style.color =
            color;

    }


    // ==========================================
    // RENDER
    // ==========================================
    new ApexCharts(
        chartElement,
        tanamanChart
    ).render();


    // ==========================================
    // PERSENTASE REALISASI
    // ==========================================
    const iconEl =
        document.querySelector(
            "#tanamanIconiGAP"
        );

    const valueEl =
        document.querySelector(
            "#tanamanValueGAP"
        );


    if (valueEl) {

        let persentase =
            target > 0
                ? (realisasiTanam / target) * 100
                : 0;


        persentase =
            Math.min(100, persentase);


        valueEl.textContent =
            persentase.toFixed(2) + "%";


        // ======================================
        // ICON
        // ======================================
        if (iconEl) {

            if (persentase >= 50) {

                iconEl.className =
                    "ti ti-arrow-up-left text-success";

            } else {

                iconEl.className =
                    "ti ti-arrow-down-right text-danger";

            }

        }

    }

});