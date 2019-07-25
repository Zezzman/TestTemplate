function RemoteAPI(){
    var token = $('meta[name="token"]').attr('content');
    var listeners = {};

    var Call = function (uri, data, method, success = null, error = null) {
        $.ajax({
            headers : {
                'token': token,
            },
            url: domain + uri,
            type: method,
            dataType: "json",
            data: data,
            success: function (data, status, response) {
                if (success)
                    success(data, response, status);
                return true;
            },
            error: function (response) {
                if (error)
                    error(response);
                return false;
            }
        });
    };
    var Listen = function (uri, data, method = "GET", interval = 5000, success = null, error = null) {
        var Action = function () {
            Call(uri, data, method, function (data, response, status) {
                if (success)
                    return success(data, response, status);
            }, function (response) {
                CloseListener(uri);
                if (error)
                    error(response);
            });
        };
        if (listeners.hasOwnProperty(uri)) {
            Action();
            listeners[uri] = Action;
        } else {
            listeners[uri] = Action;
            Wait(interval, function () {
                if (listeners.hasOwnProperty(uri)
                && listeners[uri]) {
                    return listeners[uri]();
                } else {
                    return false;
                }
            }, true);
        }
    };
    var CloseListener = function (uri) {
        if (listeners.hasOwnProperty(uri)) {
            listeners[uri] = false;
            return true;
        } else {
            return false;
        }
    };
    var Wait = function (interval, fallback, startExecute = false) {
        if (startExecute) {
            if (fallback() === false) {
                return false;
            }
        }
        setTimeout(function () {
            if (fallback() !== false) {
                Wait(interval, fallback, false);
            } else {
                return false;
            }
        }, interval);
    };
    return {
        Call: Call,
        Listen: Listen,
        CloseListener: CloseListener,
        listeners: listeners,
    };
}