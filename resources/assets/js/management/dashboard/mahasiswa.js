/**
 * Student Dashboard - Service Tickets
 */

'use strict';

(function () {
  let cardColor, headingColor, fontFamily, labelColor;
  cardColor = config.colors.cardColor;
  labelColor = config.colors.textMuted;
  headingColor = config.colors.headingColor;

  // Swiper loop and autoplay
  const swiperWithPagination = document.querySelector('#swiper-with-pagination-cards');
  if (swiperWithPagination) {
    new Swiper(swiperWithPagination, {
      loop: true,
      autoplay: {
        delay: 2500,
        disableOnInteraction: false
      },
      pagination: {
        clickable: true,
        el: '.swiper-pagination'
      }
    });
  }

  // Average Tickets Per Month (Area Chart)
  const averageTicketsPerMonthEl = document.querySelector('#averageTicketsPerMonth'),
    averageTicketsPerMonthConfig = {
      chart: {
        height: 105,
        type: 'area',
        toolbar: {
          show: false
        },
        sparkline: {
          enabled: true
        }
      },
      markers: {
        colors: 'transparent',
        strokeColors: 'transparent'
      },
      grid: {
        show: false
      },
      colors: [config.colors.success],
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.4,
          gradientToColors: [config.colors.cardColor],
          opacityTo: 0.1,
          stops: [0, 100]
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 2,
        curve: 'smooth'
      },
      series: [
        {
          data: window.dashboardData.weeklyData || [0, 0, 0, 0, 0, 0, 0]
        }
      ],
      xaxis: {
        show: true,
        lines: {
          show: false
        },
        labels: {
          show: false
        },
        stroke: {
          width: 0
        },
        axisBorder: {
          show: false
        }
      },
      yaxis: {
        stroke: {
          width: 0
        },
        show: false
      },
      tooltip: {
        enabled: false
      },
      responsive: [
        {
          breakpoint: 1387,
          options: {
            chart: {
              height: 80
            }
          }
        },
        {
          breakpoint: 1200,
          options: {
            chart: {
              height: 123
            }
          }
        }
      ]
    };
  if (typeof averageTicketsPerMonthEl !== undefined && averageTicketsPerMonthEl !== null) {
    const averageTicketsPerMonth = new ApexCharts(averageTicketsPerMonthEl, averageTicketsPerMonthConfig);
    averageTicketsPerMonth.render();
  }

  // Weekly Ticket Report (Bar Chart)
  const weeklyTicketReportEl = document.querySelector('#weeklyTicketReport');

  // Get current day (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
  const currentDay = new Date().getDay();

  // Adjust index for array starting from Monday (index 0)
  const activeDayInChart = currentDay === 0 ? 6 : currentDay - 1;

  // Create color array with active day based on current day
  const dayColors = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'].map((dayName, dayIndex) => {
    return dayIndex === activeDayInChart ? config.colors.primary : config.colors_label.primary;
  });

  const weeklyTicketReportConfig = {
    chart: {
      height: 161,
      parentHeightOffset: 0,
      type: 'bar',
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        barHeight: '60%',
        columnWidth: '38%',
        startingShape: 'rounded',
        endingShape: 'rounded',
        borderRadius: 4,
        distributed: true
      }
    },
    grid: {
      show: false,
      padding: {
        top: -30,
        bottom: 0,
        left: -10,
        right: -10
      }
    },
    colors: dayColors,
    dataLabels: {
      enabled: false
    },
    series: [
      {
        name: 'Ticket Count',
        data: window.dashboardData.weeklyData || [0, 0, 0, 0, 0, 0, 0]
      }
    ],
    legend: {
      show: false
    },
    xaxis: {
      categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      },
      labels: {
        style: {
          colors: labelColor,
          fontSize: '13px',
          fontFamily: fontFamily
        }
      }
    },
    yaxis: {
      labels: {
        show: false
      }
    },
    tooltip: {
      enabled: false
    },
    responsive: [
      {
        breakpoint: 1025,
        options: {
          chart: {
            height: 199
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

  if (typeof weeklyTicketReportEl !== undefined && weeklyTicketReportEl !== null) {
    const weeklyTicketReport = new ApexCharts(weeklyTicketReportEl, weeklyTicketReportConfig);
    weeklyTicketReport.render();
  }

  // Ticket Completion Tracker (Radial Bar Chart)
  const ticketCompletionTrackerEl = document.querySelector('#ticketCompletionTracker'),
    ticketCompletionTrackerOptions = {
      series: [window.dashboardData.persentaseSelesai || 0],
      labels: ['Tiket Selesai'],
      chart: {
        height: 337,
        type: 'radialBar'
      },
      plotOptions: {
        radialBar: {
          offsetY: 10,
          startAngle: -140,
          endAngle: 130,
          hollow: {
            size: '65%'
          },
          track: {
            background: cardColor,
            strokeWidth: '100%'
          },
          dataLabels: {
            name: {
              offsetY: -20,
              color: labelColor,
              fontSize: '13px',
              fontWeight: '400',
              fontFamily: fontFamily
            },
            value: {
              offsetY: 10,
              color: headingColor,
              fontSize: '38px',
              fontWeight: '400',
              fontFamily: fontFamily
            }
          }
        }
      },
      colors: [config.colors.success],
      fill: {
        type: 'gradient',
        gradient: {
          shade: 'dark',
          shadeIntensity: 0.5,
          gradientToColors: [config.colors.success],
          inverseColors: true,
          opacityFrom: 1,
          opacityTo: 0.6,
          stops: [30, 70, 100]
        }
      },
      stroke: {
        dashArray: 10
      },
      grid: {
        padding: {
          top: -20,
          bottom: 5
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
      },
      responsive: [
        {
          breakpoint: 1025,
          options: {
            chart: {
              height: 330
            }
          }
        },
        {
          breakpoint: 769,
          options: {
            chart: {
              height: 280
            }
          }
        }
      ]
    };
  if (typeof ticketCompletionTrackerEl !== undefined && ticketCompletionTrackerEl !== null) {
    const ticketCompletionTracker = new ApexCharts(ticketCompletionTrackerEl, ticketCompletionTrackerOptions);
    ticketCompletionTracker.render();
  }
})();
