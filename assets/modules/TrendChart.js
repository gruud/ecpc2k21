
export class TrendChart {

    constructor() {
    }
    
    init () {
        const self = this;
        $(document).ready(function () {
            const trendData = $('canvas#trendsChart');

            if (trendData.length){
                const data = trendData.attr('data-chart');
                console.log(data);
                if (data !== undefined) {
                    self.createTrendChart(JSON.parse(data));
                }
            }
        });
    }

    createTrendChart(trendData) {
        // For a pie chart
        let ctx = document.getElementById('trendsChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: trendData,
            options: {}
        });
    }
}