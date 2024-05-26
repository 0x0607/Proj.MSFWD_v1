// const APIURL = localStorage.getItem("APIURL");
// const DEBUG = localStorage.getItem("DEBUG");
/****************************************************************************************
 * 
 * 未命名
 * LastUpdate 03/16/2024
 * 
 ****************************************************************************************/

Chart.defaults.color = "#FFF";
function getChart(chartElementId,action = "GET_TOTAL_AMOUNTS") {
  const chartElement = document.getElementById(chartElementId);
  postData({ action: action }).then(response => {
    var dataObj = JSON.parse(response.data);
    const yearData = Object.values(dataObj.year);
    const lastYearData = Object.values(dataObj.lastyear);

    new Chart(chartElement, {
      type: "line",
      data: {
        labels: [
          "　一月",
          "　二月",
          "　三月",
          "　四月",
          "　五月",
          "　六月",
          "　七月",
          "　八月",
          "　九月",
          "　十月",
          "十一月",
          "十二月",
        ],
        datasets: [
          {
            label: "Year",
            data: yearData,
            backgroundColor: "white",
            borderColor: "#F4D03F",
            borderRadius: 6,
            cubicInterpolationMode: 'monotone',
            fill: false,
            borderSkipped: false,
          },
          {
            label: "last Year",
            data: lastYearData,
            backgroundColor: "white",
            borderColor: "#939391",
            borderRadius: 6,
            cubicInterpolationMode: 'monotone',
            fill: false,
            borderSkipped: false,
          },
        ],
      },
      options: {
        interaction: {
          intersect: false,
          mode: 'index'
        },
        elements: {
          point: {
            radius: 0
          }
        },
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
          title: {
            display: true,
            text: "財務報表",
            padding: {
              bottom: 16,
            },
            font: {
              size: 16,
              weight: "normal",
            },
          },
          tooltip: {
            backgroundColor: "#FDCA49",
            bodyColor: "#0E0A03",
            yAlign: "bottom",
            cornerRadius: 4,
            titleColor: "#0E0A03",
            usePointStyle: true,
            callbacks: {
              label: function (context) {
                if (context.parsed.y !== null) {
                  const label = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                  return label;
                }
                return null;
              }
            }
          },
        },
        scales: {
          x: {
            border: {
              dash: [2, 4],
            },
            title: {
              text: "2023",
            },
          },
          y: {
            grid: {
              color: "#27292D",
            },
            border: {
              dash: [2, 4],
            },
            title: {
              display: true,
              text: "新台幣 [NTD]",
            },
          },
        },
      },
    });
  });
}

getChart("chart");