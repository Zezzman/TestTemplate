function DateFormatter(date){
    
    date = (function () { if (date == null) return now(); else return new Date(date); })();

    var Month = function (month) {
        
        const months = [
            "January", "February", "March",
            "April", "May", "June", "July",
            "August", "September", "October",
            "November", "December"
        ];
        
        return months[parseInt(month, 10)];
    }
    var MonthShort = function (month) {
        
        const monthShorts = [
            "Jan", "Feb", "Mar", "Apr",
            "May", "Jun", "Jul", "Aug",
            "Sep", "Oct", "Nov", "Dec"
        ];
        return monthShorts[parseInt(month, 10)];
    }
    var Format = function (fallback) {
        var dayIndex = date.getDate();
        var monthIndex = date.getMonth();
        var day = (dayIndex > 9)? date.getDate().toString() : "0" + dayIndex;
        var month = (monthIndex > 8)? (date.getMonth() + 1).toString() : "0" + (monthIndex + 1);
        var year = date.getFullYear();

        return fallback(day, month, year, dayIndex, monthIndex);
    }
    var ToString = function () {
        return date.toString();
    }
  
    return {
        format: Format,
        toString: ToString,
        month: Month,
        monthShort: MonthShort,
    };
}
