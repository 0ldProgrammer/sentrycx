import { Component, OnInit, Input } from '@angular/core';

import * as Chartist from 'chartist';

@Component({
    selector: 'charts-graph-component',
    templateUrl: './charts-graph.component.html',
    styleUrls : ['./charts-graph.component.css']
})

export class ChartsGraphComponent implements OnInit {



    @Input('Title') title : string;
    @Input('Subtitle') sub : string;
    @Input('data') chartDetails : any;
    @Input('legend') legend :boolean;
    public details : any;
    public showArea : boolean ;
    
    lineChartGenerator(chart: any) {
        let seq: number, delays: number, durations: number;
        seq = 0;
        delays = 80;
        durations = 500;
        chart.on('draw', function(data: any) {

          if (data.type === 'line' || data.type === 'area') {
            data.element.animate({
              d: {
                begin: 600,
                dur: 700,
                from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                to: data.path.clone().stringify(),
                easing: Chartist.Svg.Easing.easeOutQuint
              }
            });
          } else if (data.type === 'point') {
                seq++;
                data.element.animate({
                  opacity: {
                    begin: seq * delays,
                    dur: durations,
                    from: 0,
                    to: 1,
                    easing: 'ease'
                  }
                });
            }
        });

        seq = 0;
    }

    ngOnInit() {
         /* ----------==========    Rounded Line Chart initialization    ==========---------- */
         this.details = this.chartDetails.data;
         this.showArea = (this.legend?false:true);
        console.log(this.details)
         

        const dataRoundedLineChart = {
            labels: this.chartDetails.label,
            series: this.details
        };

        const min = Math.min(...(this.legend?this.details:this.details[0]));
        const max = Math.max(...(this.legend?this.details:this.details[0]));
        console.log(max)
        var mbps = (this.legend?' Mbps':'');
        const optionsRoundedLineChart: any = {
            lineSmooth: Chartist.Interpolation.cardinal({
                tension: 5
            }),
            axisX: {
                showGrid: true,
                divisor : 2,
            },
            axisY: {
                showGrid: true,
                stretch : true,
                scaleMinSpace : 15,
                offset : 80,
                labelInterpolationFnc: function(value) {
                  return value + mbps
                },
            },
            low: ((min-0.5)<1?1:(min-0.5)),
            high: (this.legend?max+1:max+0.1), // creative tim: we recommend you to set the high sa the biggest value + something for a better look
            chartPadding: { top: 0, right: 0, bottom: 0, left: 0},
            showPoint: true,
            showLine: true,
            height:200,
            showArea : true
        };

        const SentryChart = new Chartist.Line('#SentryChart', dataRoundedLineChart, optionsRoundedLineChart);

        this.lineChartGenerator(SentryChart);
    }
}