<template>
    <div class="donut-chart-container">
      <b-card 
        class="shadow-sm border-0" 
        :class="{'card-hover': hoverEffect}"
      >
        <b-card-title class="text-center mb-4">
          {{ title }}
        </b-card-title>
        
        <apexchart
          type="donut"
          :height="height"
          :options="chartOptions"
          :series="series"
        />
        
        <div v-if="showLegend" class="mt-3">
          <b-badge 
            v-for="(item, index) in legendItems" 
            :key="index"
            :style="{ backgroundColor: chartOptions.colors[index] }"
            class="me-2 mb-2 legend-badge"
          >
            {{ item }}
          </b-badge>
        </div>
      </b-card>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  import ApexCharts from 'apexcharts'
  
  const props = defineProps({
    title: {
      type: String,
      default: 'Распределение данных'
    },
    series: {
      type: Array,
      default: () => [44, 55, 13, 43]
    },
    labels: {
      type: Array,
      default: () => ['Team A', 'Team B', 'Team C', 'Team D']
    },
    height: {
      type: Number,
      default: 350
    },
    colors: {
      type: Array,
      default: () => ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
    },
    showLegend: {
      type: Boolean,
      default: true
    },
    hoverEffect: {
      type: Boolean,
      default: true
    }
  })
  
  const chartOptions = computed(() => ({
    chart: {
      type: 'donut',
      animations: {
        enabled: true,
        easing: 'easeinout',
        speed: 800
      }
    },
    labels: props.labels,
    colors: props.colors,
    plotOptions: {
      pie: {
        donut: {
          size: '65%',
          labels: {
            show: true,
            name: {
              show: true,
              fontSize: '14px',
              fontWeight: 600,
              color: '#6c757d'
            },
            value: {
              show: true,
              fontSize: '20px',
              fontWeight: 'bold',
              color: '#495057'
            },
            total: {
              show: true,
              label: 'Всего',
              color: '#6c757d',
              formatter: function (w) {
                return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
              }
            }
          }
        }
      }
    },
    dataLabels: {
      enabled: true,
      style: {
        fontSize: '12px',
        fontWeight: 'bold'
      },
      dropShadow: {
        enabled: true,
        top: 1,
        left: 1,
        blur: 1,
        opacity: 0.45
      }
    },
    stroke: {
      width: 2,
      colors: ['#fff']
    },
    legend: {
      show: false
    },
    tooltip: {
      y: {
        formatter: function (value) {
          return value
        }
      }
    },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: '100%'
        },
        legend: {
          position: 'bottom'
        }
      }
    }]
  }))
  
  const legendItems = computed(() => props.labels.map((label, index) => 
    `${label}: ${props.series[index]}`
  ))
  </script>
  
  <style scoped>
  .donut-chart-container {
    max-width: 100%;
  }
  
  .card-hover {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  }
  
  .card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
  }
  
  .legend-badge {
    color: white;
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
    border-radius: 0.375rem;
  }
  </style>