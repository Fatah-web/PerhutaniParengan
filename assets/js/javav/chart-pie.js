
document.addEventListener("DOMContentLoaded", function () {

  if (typeof dashboardData === "undefined") return;

  var pieChart = {
    chart: {
      type: "pie",
      height: 260,
      fontFamily: "Plus Jakarta Sans, sans-serif",
      foreColor: "#c6d1e9"
    },

    series: [
      dashboardData.status_pending,
      dashboardData.status_verified,
      dashboardData.status_rejected
    ],

    labels: ["Pending", "Verified", "Rejected"],

    colors: [
      "#FFC107",
      "#4CAF50",
      "#F44336"
    ],

    legend: {
      position: "top"
    },

    tooltip: {
      theme: "dark",
      y: {
        formatter: function(v) {
          return v.toLocaleString("id-ID") + " Data";
        }
      }
    }

  };

  new ApexCharts(document.querySelector("#chartStatus"), pieChart).render();

});