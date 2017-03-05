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

        var graphReq = new UserKitRequest('graph.visitors');
        graphReq.onSuccess(function (result) {
            var labelData = [];
            var rowData = [];

            for (var key in result.data) {
                var value = result.data[key];

                labelData.push(key);
                rowData.push(value);
            }

            var data = {
                labels: labelData,
                datasets: [
                    {
                        label: "Visitors",
                        lineTension: .1,
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
                        data: rowData,
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
        });
        graphReq.fire();
        console.log(graphReq);
    }
};

var UserKitRequest = function (type) {
    this.type = type;

    this.callbacksSuccess = [];
    this.callbacksError = [];

    this.resultData = null;

    this.requestData = { };

    this.set = function (key, value) {
        this.resultData[key] = value;
    };

    this.onSuccess = function (callbackToAdd) {
        if (callbackToAdd) {
            // Attach callback mode
            this.callbacksSuccess.push(callbackToAdd);
        } else {
            // Trigger callback mode
            for (var i = 0; i < this.callbacksSuccess.length; i++) {
                var callback = this.callbacksSuccess[i];
                callback(this.resultData);
            }
        }
    };

    this.onError = function (callbackToAdd) {
        if (callbackToAdd) {
            // Attach callback mode
            this.callbacksError.push(callbackToAdd);
        } else {
            // Trigger callback mode
            for (var i = 0; i < this.callbacksError.length; i++) {
                var callback = this.callbacksError[i];
                callback();
            }
        }
    };

    this.fire = function () {
        var target = UserKitLoader.getBaseUrl();

        var payload = {
            type: this.type
        };

        payload = $.extend(payload, this.requestData);

        $.post(target, JSON.stringify(payload))
            .done(function (data) {
                this.resultData = data;
                this.onSuccess();
            }.bind(this))
            .fail(function () {
                this.onError();
            }.bind(this));
    };
};

$(document).ready(function () {
    UserKit.bootstrap();
});