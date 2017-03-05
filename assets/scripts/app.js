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

        Chart.defaults.global.legend.display = false;

        var graphReq = new UserKitRequest('graph.visitors');
        graphReq.onSuccess(function (result) {
            var labelData = [];
            var rowData = [];
            var maxCount = 0;

            for (var key in result.data) {
                var value = result.data[key];

                labelData.push(key);
                rowData.push(value);

                if (value > maxCount) {
                    maxCount = value;
                }
            }

            var data = {
                labels: labelData,
                datasets: [
                    {
                        label: 'Visitors',
                        lineTension: 0,
                        backgroundColor: "rgba(52, 152, 219, .25)",
                        borderColor: "#2980b9",
                        borderCapStyle: 'butt',
                        borderJoinStyle: 'miter',
                        pointBorderColor: "#2980b9",
                        pointBorderWidth: 6,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: "#2980b9",
                        pointHoverBorderColor: "#fff",
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