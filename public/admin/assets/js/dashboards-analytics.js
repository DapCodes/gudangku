/**
 * Dashboard Analytics
 */

'use strict';

(function () {
  let cardColor, headingColor, axisColor, shadeColor, borderColor;

  cardColor = config.colors.white;
  headingColor = config.colors.headingColor;
  axisColor = config.colors.axisColor;
  borderColor = config.colors.borderColor;

  // Total Revenue Report Chart - Bar Chart
  // --------------------------------------------------------------------
  const totalRevenueChartEl = document.querySelector('#totalRevenueChart'),
    totalRevenueChartOptions = {
      series: [
        {
          name: '2021',
          data: [18, 7, 15, 29, 18, 12, 9]
        },
        {
          name: '2020',
          data: [-13, -18, -9, -14, -5, -17, -15]
        }
      ],
      chart: {
        height: 300,
        stacked: true,
        type: 'bar',
        toolbar: { show: false }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '33%',
          borderRadius: 12,
          startingShape: 'rounded',
          endingShape: 'rounded'
        }
      },
      colors: [config.colors.primary, config.colors.info],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 6,
        lineCap: 'round',
        colors: [cardColor]
      },
      legend: {
        show: true,
        horizontalAlign: 'left',
        position: 'top',
        markers: {
          height: 8,
          width: 8,
          radius: 12,
          offsetX: -3
        },
        labels: {
          colors: axisColor
        },
        itemMargin: {
          horizontal: 10
        }
      },
      grid: {
        borderColor: borderColor,
        padding: {
          top: 0,
          bottom: -8,
          left: 20,
          right: 20
        }
      },
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        labels: {
          style: {
            fontSize: '13px',
            colors: axisColor
          }
        },
        axisTicks: {
          show: false
        },
        axisBorder: {
          show: false
        }
      },
      yaxis: {
        labels: {
          style: {
            fontSize: '13px',
            colors: axisColor
          }
        }
      },
      responsive: [
        {
          breakpoint: 1700,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '32%'
              }
            }
          }
        },
        {
          breakpoint: 1580,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '35%'
              }
            }
          }
        },
        {
          breakpoint: 1440,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '42%'
              }
            }
          }
        },
        {
          breakpoint: 1300,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '48%'
              }
            }
          }
        },
        {
          breakpoint: 1200,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '40%'
              }
            }
          }
        },
        {
          breakpoint: 1040,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 11,
                columnWidth: '48%'
              }
            }
          }
        },
        {
          breakpoint: 991,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '30%'
              }
            }
          }
        },
        {
          breakpoint: 840,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '35%'
              }
            }
          }
        },
        {
          breakpoint: 768,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '28%'
              }
            }
          }
        },
        {
          breakpoint: 640,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '32%'
              }
            }
          }
        },
        {
          breakpoint: 576,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '37%'
              }
            }
          }
        },
        {
          breakpoint: 480,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '45%'
              }
            }
          }
        },
        {
          breakpoint: 420,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '52%'
              }
            }
          }
        },
        {
          breakpoint: 380,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '60%'
              }
            }
          }
        }
      ],
      states: {
        hover: {
          filter: {
            type: 'none'
          }
        },
        active: {
          filter: {
            type: 'none'
          }
        }
      }
    };
  if (typeof totalRevenueChartEl !== undefined && totalRevenueChartEl !== null) {
    const totalRevenueChart = new ApexCharts(totalRevenueChartEl, totalRevenueChartOptions);
    totalRevenueChart.render();
  }

  // Growth Chart - Radial Bar Chart
  // --------------------------------------------------------------------
  const growthChartEl = document.querySelector('#growthChart'),
    growthChartOptions = {
      series: [78],
      labels: ['Growth'],
      chart: {
        height: 240,
        type: 'radialBar'
      },
      plotOptions: {
        radialBar: {
          size: 150,
          offsetY: 10,
          startAngle: -150,
          endAngle: 150,
          hollow: {
            size: '55%'
          },
          track: {
            background: cardColor,
            strokeWidth: '100%'
          },
          dataLabels: {
            name: {
              offsetY: 15,
              color: headingColor,
              fontSize: '15px',
              fontWeight: '600',
              fontFamily: 'Public Sans'
            },
            value: {
              offsetY: -25,
              color: headingColor,
              fontSize: '22px',
              fontWeight: '500',
              fontFamily: 'Public Sans'
            }
          }
        }
      },
      colors: [config.colors.primary],
      fill: {
        type: 'gradient',
        gradient: {
          shade: 'dark',
          shadeIntensity: 0.5,
          gradientToColors: [config.colors.primary],
          inverseColors: true,
          opacityFrom: 1,
          opacityTo: 0.6,
          stops: [30, 70, 100]
        }
      },
      stroke: {
        dashArray: 5
      },
      grid: {
        padding: {
          top: -35,
          bottom: -10
        }
      },
      states: {
        hover: {
          filter: {
            type: 'none'
          }
        },
        active: {
          filter: {
            type: 'none'
          }
        }
      }
    };
  if (typeof growthChartEl !== undefined && growthChartEl !== null) {
    const growthChart = new ApexCharts(growthChartEl, growthChartOptions);
    growthChart.render();
  }

  // Profit Report Line Chart
  // --------------------------------------------------------------------
  

  // Order Statistics Chart
  // --------------------------------------------------------------------

  

  // Income Chart - Area chart
  // --------------------------------------------------------------------
  // const incomeChartEl = document.querySelector('#incomeChart'),
  //   incomeChartConfig = {
  //     series: [
  //       {
  //         data: [24, 21, 30, 22, 42, 26, 35, 29]
  //       }
  //     ],
  //     chart: {
  //       height: 215,
  //       parentHeightOffset: 0,
  //       parentWidthOffset: 0,
  //       toolbar: {
  //         show: false
  //       },
  //       type: 'area'
  //     },
  //     dataLabels: {
  //       enabled: false
  //     },
  //     stroke: {
  //       width: 2,
  //       curve: 'smooth'
  //     },
  //     legend: {
  //       show: false
  //     },
  //     markers: {
  //       size: 6,
  //       colors: 'transparent',
  //       strokeColors: 'transparent',
  //       strokeWidth: 4,
  //       discrete: [
  //         {
  //           fillColor: config.colors.white,
  //           seriesIndex: 0,
  //           dataPointIndex: 7,
  //           strokeColor: config.colors.primary,
  //           strokeWidth: 2,
  //           size: 6,
  //           radius: 8
  //         }
  //       ],
  //       hover: {
  //         size: 7
  //       }
  //     },
  //     colors: [config.colors.primary],
  //     fill: {
  //       type: 'gradient',
  //       gradient: {
  //         shade: shadeColor,
  //         shadeIntensity: 0.6,
  //         opacityFrom: 0.5,
  //         opacityTo: 0.25,
  //         stops: [0, 95, 100]
  //       }
  //     },
  //     grid: {
  //       borderColor: borderColor,
  //       strokeDashArray: 3,
  //       padding: {
  //         top: -20,
  //         bottom: -8,
  //         left: -10,
  //         right: 8
  //       }
  //     },
  //     xaxis: {
  //       categories: ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
  //       axisBorder: {
  //         show: false
  //       },
  //       axisTicks: {
  //         show: false
  //       },
  //       labels: {
  //         show: true,
  //         style: {
  //           fontSize: '13px',
  //           colors: axisColor
  //         }
  //       }
  //     },
  //     yaxis: {
  //       labels: {
  //         show: false
  //       },
  //       min: 10,
  //       max: 50,
  //       tickAmount: 4
  //     }
  //   };
  // if (typeof incomeChartEl !== undefined && incomeChartEl !== null) {
  //   const incomeChart = new ApexCharts(incomeChartEl, incomeChartConfig);
  //   incomeChart.render();
  // }

  // Expenses Mini Chart - Radial Chart
  // --------------------------------------------------------------------
  // const weeklyExpensesEl = document.querySelector('#expensesOfWeek'),
  //   weeklyExpensesConfig = {
  //     series: [65],
  //     chart: {
  //       width: 60,
  //       height: 60,
  //       type: 'radialBar'
  //     },
  //     plotOptions: {
  //       radialBar: {
  //         startAngle: 0,
  //         endAngle: 360,
  //         strokeWidth: '8',
  //         hollow: {
  //           margin: 2,
  //           size: '45%'
  //         },
  //         track: {
  //           strokeWidth: '50%',
  //           background: borderColor
  //         },
  //         dataLabels: {
  //           show: true,
  //           name: {
  //             show: false
  //           },
  //           value: {
  //             formatter: function (val) {
  //               return '$' + parseInt(val);
  //             },
  //             offsetY: 5,
  //             color: '#697a8d',
  //             fontSize: '13px',
  //             show: true
  //           }
  //         }
  //       }
  //     },
  //     fill: {
  //       type: 'solid',
  //       colors: config.colors.primary
  //     },
  //     stroke: {
  //       lineCap: 'round'
  //     },
  //     grid: {
  //       padding: {
  //         top: -10,
  //         bottom: -15,
  //         left: -10,
  //         right: -10
  //       }
  //     },
  //     states: {
  //       hover: {
  //         filter: {
  //           type: 'none'
  //         }
  //       },
  //       active: {
  //         filter: {
  //           type: 'none'
  //         }
  //       }
  //     }
  //   };
  // if (typeof weeklyExpensesEl !== undefined && weeklyExpensesEl !== null) {
  //   const weeklyExpenses = new ApexCharts(weeklyExpensesEl, weeklyExpensesConfig);
  //   weeklyExpenses.render();
  // }
})();
