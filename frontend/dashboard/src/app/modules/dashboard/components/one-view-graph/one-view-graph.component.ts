import { Component, Input } from '@angular/core';
import { HistoricalService } from '@app/services';
import { Chart, registerables  } from 'chart.js';
import colorLib from '@kurkle/color';


@Component({
    selector: 'one-view-graph',
    templateUrl: './one-view-graph.component.html'
})
export class OneViewGraphComponent {

    @Input('data-worker-id') workerID : String;

    constructor(
        private service : HistoricalService
    ) {
         Chart.register(...registerables); 
    }

    public CHART_COLORS = {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(201, 203, 207)'
      };

    public time;
    public getLabels;

    public jitterChart;
    public averageLatencyChart;
    public packetLossChart;
    public mosChart;

    public jitter;
    public average_latency;
    public packet_loss;
    public mos;

    public currentDate = new Date();
    
    ngAfterViewInit() { 
        this.initialLoad();
    }
      
    transparentize(value, opacity) {
        var alpha = opacity === undefined ? 0.5 : 1 - opacity;
        return colorLib(value).alpha(alpha).rgbString();
      }

    public timerRange(value) {
        this.time = value;
        this.listOfData();
    }

    public initialLoad() {
        this.time = 0;
        let today = new Date();
        let date = today.getFullYear() + '-' + ('0' + (today.getMonth()+1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
        let time = ('0' + today.getHours()).slice(-2) + ":" + ('0' + today.getMinutes()).slice(-2) + ":" + ('0' + today.getSeconds()).slice(-2);
        let date_time = date + ' ' + time;
        
        const self = this;
        this.service.getHistoricalData(this.workerID, this.time, date_time).subscribe( response => {
            this.getLabels = response['labels'].reverse();
            this.jitter =  response['logs']['jitter_results'].reverse();
            this.average_latency = response['logs']['average_latency_results'].reverse();
            this.packet_loss = response['logs']['packet_loss_results'].reverse();
            this.mos = response['logs']['mos_results'].reverse();
            this.jitterGraph();
            this.averageLatencyGraph();
            this.packetLossGraph();
            this.mosGraph();
        });
    }

    public jitterGraph() {

        const canvas = <HTMLCanvasElement> document.getElementById('jitterChart');
        const jitterContext = canvas.getContext('2d');
        const labels = this.getLabels;
        
        this.jitterChart = new Chart(jitterContext, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                    label: 'Jitter',
                    data: this.jitter,
                    borderColor: this.CHART_COLORS.red,
                    backgroundColor: this.transparentize(this.CHART_COLORS.red, 0.5),
                    fill: true
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value){return value+ "ms"},
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: "Time (24 Hour format)"
                        }
                    }
                },
                responsive: true,
                plugins: {
                  legend: {
                    position: 'top',
                  },
                  title: {
                    display: true,
                    text: 'Jitter'
                  },
                  tooltip: {
                    mode: 'index',
                    intersect: false
                  }
                }
            }  
        });

    }

    public averageLatencyGraph() {
        const canvas = <HTMLCanvasElement> document.getElementById('averageLatencyChart');
        const averageLatencyContext = canvas.getContext('2d');
        const labels = this.getLabels;

        this.averageLatencyChart = new Chart(averageLatencyContext, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                    label: 'Average Latency',
                    data: this.average_latency,
                    borderColor: this.CHART_COLORS.blue,
                    backgroundColor: this.transparentize(this.CHART_COLORS.blue, 0.5),
                    fill: true
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        title: {
                            display: true,
                            text: "Time (24 Hour format)"
                        }
                    }
                },
                responsive: true,
                plugins: {
                  legend: {
                    position: 'top',
                  },
                  title: {
                    display: true,
                    text: 'Latency'
                  },
                  tooltip: {
                    mode: 'index',
                    intersect: false
                  }
                }
            }  
        });

    }

    public packetLossGraph() {
        const canvas = <HTMLCanvasElement> document.getElementById('packetLossChart');
        const packetLossContext = canvas.getContext('2d');
        const labels = this.getLabels;
        
        this.packetLossChart = new Chart(packetLossContext, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                    label: 'Packet Loss',
                    data: this.packet_loss,
                    borderColor: this.CHART_COLORS.yellow,
                    backgroundColor: this.transparentize(this.CHART_COLORS.yellow, 0.5),
                    fill: true
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value){return value+ "%"},
                        },
                        title: {
                            display: true,
                            text: "Percentage"
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: "Time (24 Hour format)"
                        }
                    }
                },
                responsive: true,
                plugins: {
                  legend: {
                    position: 'top',
                  },
                  title: {
                    display: true,
                    text: 'Packet Loss'
                  },
                  tooltip: {
                    callbacks: {
                        label: (item) =>
                            `${item.dataset.label}: ${item.formattedValue} %`,
                    },
                    mode: 'index',
                    intersect: false
                  }
                }
            }  
        });

    }

    public mosGraph() {
        const canvas = <HTMLCanvasElement> document.getElementById('mosChart');
        const mosContext = canvas.getContext('2d');
        const labels = this.getLabels;

        this.mosChart = new Chart(mosContext, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                    label: 'Mean Opinion Score',
                    data: this.mos,
                    borderColor: this.CHART_COLORS.green,
                    backgroundColor: this.transparentize(this.CHART_COLORS.green, 0.5),
                    fill: true
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        title: {
                            display: true,
                            text: "Time (24 Hour format)"
                        }
                    }
                },
                responsive: true,
                plugins: {
                  legend: {
                    position: 'top',
                  },
                  title: {
                    display: true,
                    text: 'Mean Opinion Score'
                  },
                  tooltip: {
                    mode: 'index',
                    intersect: false
                  }
                }
            }  
        });

    }

    public listOfData(){
        let today = new Date();
        let date = today.getFullYear() + '-' + ('0' + (today.getMonth()+1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
        let time = ('0' + today.getHours()).slice(-2) + ":" + ('0' + today.getMinutes()).slice(-2) + ":" + ('0' + today.getSeconds()).slice(-2);
        let date_time = date + ' ' + time;
        
        const self = this;
        this.service.getHistoricalData(this.workerID, this.time, date_time).subscribe( response => {
            this.getLabels = response['labels'].reverse();
            this.jitter =  response['logs']['jitter_results'].reverse();
            this.average_latency = response['logs']['average_latency_results'].reverse();
            this.packet_loss = response['logs']['packet_loss_results'].reverse();
            this.mos = response['logs']['mos_results'].reverse();

            this.jitterChart.destroy();
            this.averageLatencyChart.destroy();
            this.mosChart.destroy();
            this.packetLossChart.destroy();
            this.jitterGraph();
            this.averageLatencyGraph();
            this.packetLossGraph();
            this.mosGraph();
        });
       
       }
}
