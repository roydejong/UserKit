var UserKit = {
    bootstrap: function () {
        UserKitLoader.loadCss('lib/bootstrap/bootstrap.min.css', function () {
            UserKitLoader.loadCss('style/userkit.css');
        });

        UserKitLoader.allReady(function () {
            UserKit.pageReady();
        });
    },

    pageReady: function () {
        $('#loader').hide();
        $('#main').fadeIn('fast');

        var data = {
            labels: ["January", "February", "March", "April", "May", "June", "July"],
            datasets: [
                {
                    label: "Visitors",
                    lineTension: 0.1,
                    backgroundColor: "rgba(75,192,192,0.4)",
                    borderColor: "rgba(75,192,192,1)",
                    borderCapStyle: 'butt',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: "rgba(75,192,192,1)",
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(75,192,192,1)",
                    pointHoverBorderColor: "rgba(220,220,220,1)",
                    pointHoverBorderWidth: 2,
                    pointRadius: 1,
                    pointHitRadius: 10,
                    data: [65, 59, 80, 81, 56, 55, 40],
                    spanGaps: false,
                    fill: true
                }
            ]
        };

        var myLineChart = new Chart($('#myChart')[0].getContext('2d'), {
            type: 'line',
            data: data,
            options: { }
        });
    }
};

$(document).ready(function () {
    UserKit.bootstrap();
});