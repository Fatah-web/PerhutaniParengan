
document.addEventListener("DOMContentLoaded", function () {

    const el = document.getElementById("chartTanam12");
    if (!el) return;


    // ==========================================
    // HITUNG GAP
    // ==========================================
    const gapData = targetData.map((target, index) => {

        const targetValue = Number(target) || 0;
        const realisasiValue = Number(realisasiData[index]) || 0;

        return Math.max(0, targetValue - realisasiValue);

    });


    // ==========================================
    // HITUNG PERSENTASE PROGRESS
    // ==========================================
    const persentaseData = targetData.map((target, index) => {

        const targetValue = Number(target) || 0;
        const realisasiValue = Number(realisasiData[index]) || 0;

        return targetValue > 0
            ? Number(
                ((realisasiValue / targetValue) * 100).toFixed(1)
            )
            : 0;

    });


    // ==========================================
    // CHART OPTIONS
    // ==========================================
    const options = {

        // ======================================
        // SERIES
        // ======================================
        series: [

            // ----------------------------------
            // TARGET
            // ----------------------------------
            {
                name: "Target Pohon",
                type: "column",
                data: targetData,
                group: "target"
            },


            // ----------------------------------
            // REALISASI
            // ----------------------------------
            {
                name: "Realisasi Tanam",
                type: "column",
                data: realisasiData,
                group: "progress"
            },


            // ----------------------------------
            // GAP
            // ----------------------------------
            {
                name: "Gap",
                type: "column",
                data: gapData,
                group: "progress"
            },


            // ----------------------------------
            // LINE PROGRESS
            // ----------------------------------
            {
                name: "Progress",
                type: "line",
                data: persentaseData
            }

        ],


        // ==========================================
        // CHART
        // ==========================================
        chart: {
            type: "line",
            height: 480,
            stacked: true,
            stackOnlyBar: true,

            toolbar: {
                show: true,
                offsetX: -5,
                offsetY: 0,

                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            },


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
            }
        },


        // ==========================================
        // LEGEND
        // ==========================================
        legend: {
            position: "top",
            horizontalAlign: "center",

            fontSize: "12px",
            fontWeight: 500,

            labels: {
                colors: "#475569"
            },

            markers: {
                width: 9,
                height: 9,
                radius: 4
            },

            itemMargin: {
                horizontal: 12,
                vertical: 4
            }
        },


        // ==========================================
        // BAR
        // ==========================================
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "50%",
                borderRadius: 8,
                borderRadiusApplication: "around",
                borderRadiusWhenStacked: "last"
            }
        },

        // ==========================================
        // DATA LABEL
        // ==========================================
        dataLabels: {

            enabled: false

        },


        // ==========================================
        // X AXIS
        // ==========================================
        xaxis: {
            categories: bkphData,

            axisBorder: {
                show: false
            },

            axisTicks: {
                show: false
            },

            labels: {
                style: {
                    colors: "#64748B",
                    fontSize: "12px",
                    fontWeight: 500
                }
            }
        },

        // ==========================================
        // Y AXIS
        // ==========================================
        yaxis: [

            // --------------------------------------
            // Y AXIS KIRI
            // JUMLAH POHON
            // --------------------------------------
            {
                seriesName: [
                    "Target Pohon",
                    "Realisasi Tanam",
                    "Gap"
                ],

                min: 0,

                labels: {
                    style: {
                        colors: "#64748B",
                        fontSize: "11px"
                    },

                    formatter: function (value) {
                        return Math.round(value)
                            .toLocaleString("id-ID");
                    }
                },

                title: {
                    text: "Jumlah Pohon",
                    style: {
                        color: "#64748B",
                        fontSize: "12px",
                        fontWeight: 600
                    }
                }
            },

            // --------------------------------------
            // Y AXIS KANAN
            // PERSENTASE
            // --------------------------------------
            {
                seriesName: "Progress",

                opposite: true,

                min: 0,
                max: 100,
                tickAmount: 5,

                labels: {
                    style: {
                        colors: "#6366F1",
                        fontSize: "11px",
                        fontWeight: 600
                    },

                    formatter: function (value) {
                        return value.toFixed(0) + "%";
                    }
                },

                title: {
                    text: "Progress",
                    style: {
                        color: "#6366F1",
                        fontSize: "12px",
                        fontWeight: 600
                    }
                }
            }

        ],


        // ==========================================
        // WARNA
        // ==========================================
        colors: [
            "#60A5FA", // Target
            "#10B981", // Realisasi
            "#FBBF24", // Gap
            "#f1637f"  // Progress
        ],


        // ==========================================
        // STROKE
        // ==========================================
        stroke: {
            width: [
                0,
                0,
                0,
                3
            ],
            curve: "smooth",
            lineCap: "round"
        },
        states: {
            hover: {
                filter: {
                    type: "lighten",
                    value: 0.05
                }
            }
        },


        // ==========================================
        // MARKER LINE
        // ==========================================
        markers: {
            size: 5,

            strokeWidth: 3,

            strokeColors: "#ffffff",

            hover: {
                size: 7
            }
        },



        // ==========================================
        // FILL
        // ==========================================
        fill: {

            opacity: [
                1,
                1,
                1,
                1
            ]

        },


        // ==========================================
        // GRID
        // ==========================================
        grid: {
            borderColor: "#E2E8F0",
            strokeDashArray: 3,

            padding: {
                top: 5,
                right: 15,
                left: 10,
                bottom: 5
            },

            xaxis: {
                lines: {
                    show: false
                }
            }
        },


        // ==========================================
        // TOOLTIP
        // ==========================================

        // ======================================
        // TOOLTIP
        // ======================================
        tooltip: {

            shared: false,

            intersect: true,

            custom: function ({
                dataPointIndex
            }) {

                const target =
                    Number(targetData[dataPointIndex]) || 0;

                const realisasi =
                    Number(realisasiData[dataPointIndex]) || 0;

                const gap =
                    Math.max(0, target - realisasi);

                const persentase =
                    target > 0
                        ? ((realisasi / target) * 100).toFixed(1)
                        : "0.0";

                const bkph =
                    bkphData[dataPointIndex] ?? "-";


                return `
                    <div style="
                        width: 250px;
                        padding: 14px 16px;
                        background: #ffffff;
                        border: 1px solid #e5e7eb;
                        border-radius: 10px;
                        box-shadow:
                            0 8px 24px
                            rgba(15, 23, 42, 0.12);
                        font-family: inherit;
                    ">

                        <div style="
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            margin-bottom:12px;
                        ">

                            <div>
                                <div style="
                                    font-size:10px;
                                    color:#94a3b8;
                                    margin-bottom:2px;
                                ">
                                    LOKASI
                                </div>

                                <div style="
                                    font-size:14px;
                                    font-weight:700;
                                    color:#1e293b;
                                ">
                                    ${bkph}
                                </div>
                            </div>

                            <div style="
                                padding:5px 9px;
                                border-radius:6px;
                                background:#f5f3ff;
                                color:#7c3aed;
                                font-size:11px;
                                font-weight:700;
                            ">
                                ${persentase}%
                            </div>

                        </div>


                        <div style="
                            border-top:1px solid #f1f5f9;
                            padding-top:10px;
                        ">

                            <!-- TARGET -->
                            <div style="
                                display:flex;
                                justify-content:space-between;
                                margin-bottom:8px;
                            ">

                                <span style="
                                    font-size:12px;
                                    color:#64748b;
                                ">
                                    🔵 Target Pohon
                                </span>

                                <strong style="
                                    font-size:13px;
                                    color:#1e293b;
                                ">
                                    ${target.toLocaleString("id-ID")}
                                </strong>

                            </div>


                            <!-- REALISASI -->
                            <div style="
                                display:flex;
                                justify-content:space-between;
                                margin-bottom:8px;
                            ">

                                <span style="
                                    font-size:12px;
                                    color:#64748b;
                                ">
                                    🟢 Realisasi Tanam
                                </span>

                                <strong style="
                                    font-size:13px;
                                    color:#1e293b;
                                ">
                                    ${realisasi.toLocaleString("id-ID")}
                                </strong>

                            </div>


                            <!-- GAP -->
                            <div style="
                                display:flex;
                                justify-content:space-between;
                                padding-top:8px;
                                border-top:1px dashed #e2e8f0;
                            ">

                                <span style="
                                    font-size:12px;
                                    color:#64748b;
                                ">
                                    🟠 Gap
                                </span>

                                <strong style="
                                    font-size:13px;
                                    color:#d97706;
                                ">
                                    ${gap.toLocaleString("id-ID")}
                                </strong>

                            </div>

                        </div>


                        <!-- PROGRESS -->
                        <div style="
                            margin-top:13px;
                        ">

                            <div style="
                                display:flex;
                                justify-content:space-between;
                                margin-bottom:5px;
                            ">

                                <span style="
                                    font-size:10px;
                                    color:#94a3b8;
                                ">
                                    Progress
                                </span>

                                <strong style="
                                    font-size:10px;
                                    color:#7c3aed;
                                ">
                                    ${persentase}%
                                </strong>

                            </div>

                            <div style="
                                height:5px;
                                width:100%;
                                background:#e2e8f0;
                                border-radius:10px;
                                overflow:hidden;
                            ">

                                <div style="
                                    height:100%;
                                    width:${Math.min(
                    100,
                    Number(persentase)
                )}%;
                                    background:#8B5CF6;
                                    border-radius:10px;
                                "></div>

                            </div>

                        </div>

                    </div>
                `;
            }

        }

    };


    // ==========================================
    // RENDER
    // ==========================================
    const chart = new ApexCharts(el, options);

    chart.render();

});