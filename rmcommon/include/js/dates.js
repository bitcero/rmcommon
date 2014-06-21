var exmDates = jQuery.extend({

    init:function(){
        
    },
    
    mktime:function(){
        // http://kevin.vanzonneveld.net
        // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   improved by: baris ozdil
        // *     example 1: mktime( 14, 10, 2, 2, 1, 2008 );
        // *     returns 1: 1201871402

        var d = new Date(),
            r = arguments,
            i = 0,
            e = ['Hours', 'Minutes', 'Seconds', 'Month', 'Date', 'FullYear'];
     
        for (i = 0; i < e.length; i++) {
            if (typeof r[i] === 'undefined') {
                r[i] = d['get' + e[i]]();
                r[i] += (i === 3); // +1 to fix JS months.
            } else {
                r[i] = parseInt(r[i], 10);
                if (isNaN(r[i])) {
                    return false;
                }
            }
        }
     
        // Map years 0-69 to 2000-2069 and years 70-100 to 1970-2000.
        r[5] += (r[5] >= 0 ? (r[5] <= 69 ? 2e3 : (r[5] <= 100 ? 1900 : 0)) : 0);
     
        // Set year, month (-1 to fix JS months), and date.
        // !This must come before the call to setHours!
        d.setFullYear(r[5], r[3] - 1, r[4]);
     
        // Set hours, minutes, and seconds.
        d.setHours(r[0], r[1], r[2]);
     
        // Divide milliseconds by 1000 to return seconds and drop decimal.
        // Add 1 second if negative or it'll be off from PHP by 1 second.
        return (d.getTime() / 1e3 >> 0) - (d.getTime() < 0);

    },

    date: function(format, timestamp){
        var that = this,
            jsdate, f, formatChr = /\\?([a-z])/gi,
            formatChrCb,
            // Keep this here (works, but for code commented-out
            // below for file size reasons)
            //, tal= [],
            _pad = function (n, c) {
                if ((n = n + "").length < c) {
                    return new Array((++c) - n.length).join("0") + n;
                } else {
                    return n;
                }
            },
            txt_words = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            txt_ordin = {
                1: "st",
                2: "nd",
                3: "rd",
                21: "st",
                22: "nd",
                23: "rd",
                31: "st"
            };
        formatChrCb = function (t, s) {
            return f[t] ? f[t]() : s;
        };
        f = {
            // Day
            d: function () { // Day of month w/leading 0; 01..31
                return _pad(f.j(), 2);
            },
            D: function () { // Shorthand day name; Mon...Sun
                return f.l().slice(0, 3);
            },
            j: function () { // Day of month; 1..31
                return jsdate.getDate();
            },
            l: function () { // Full day name; Monday...Sunday
                return txt_words[f.w()] + 'day';
            },
            N: function () { // ISO-8601 day of week; 1[Mon]..7[Sun]
                return f.w() || 7;
            },
            S: function () { // Ordinal suffix for day of month; st, nd, rd, th
                return txt_ordin[f.j()] || 'th';
            },
            w: function () { // Day of week; 0[Sun]..6[Sat]
                return jsdate.getDay();
            },
            z: function () { // Day of year; 0..365
                var a = new Date(f.Y(), f.n() - 1, f.j()),
                    b = new Date(f.Y(), 0, 1);
                return Math.round((a - b) / 864e5) + 1;
            },
     
            // Week
            W: function () { // ISO-8601 week number
                var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3),
                    b = new Date(a.getFullYear(), 0, 4);
                return 1 + Math.round((a - b) / 864e5 / 7);
            },
     
            // Month
            F: function () { // Full month name; January...December
                return txt_words[6 + f.n()];
            },
            m: function () { // Month w/leading 0; 01...12
                return _pad(f.n(), 2);
            },
            M: function () { // Shorthand month name; Jan...Dec
                return f.F().slice(0, 3);
            },
            n: function () { // Month; 1...12
                return jsdate.getMonth() + 1;
            },
            t: function () { // Days in month; 28...31
                return (new Date(f.Y(), f.n(), 0)).getDate();
            },
     
            // Year
            L: function () { // Is leap year?; 0 or 1
                return new Date(f.Y(), 1, 29).getMonth() === 1 | 0;
            },
            o: function () { // ISO-8601 year
                var n = f.n(),
                    W = f.W(),
                    Y = f.Y();
                return Y + (n === 12 && W < 9 ? -1 : n === 1 && W > 9);
            },
            Y: function () { // Full year; e.g. 1980...2010
                return jsdate.getFullYear();
            },
            y: function () { // Last two digits of year; 00...99
                return (f.Y() + "").slice(-2);
            },
     
            // Time
            a: function () { // am or pm
                return jsdate.getHours() > 11 ? "pm" : "am";
            },
            A: function () { // AM or PM
                return f.a().toUpperCase();
            },
            B: function () { // Swatch Internet time; 000..999
                var H = jsdate.getUTCHours() * 36e2,
                    // Hours
                    i = jsdate.getUTCMinutes() * 60,
                    // Minutes
                    s = jsdate.getUTCSeconds(); // Seconds
                return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
            },
            g: function () { // 12-Hours; 1..12
                return f.G() % 12 || 12;
            },
            G: function () { // 24-Hours; 0..23
                return jsdate.getHours();
            },
            h: function () { // 12-Hours w/leading 0; 01..12
                return _pad(f.g(), 2);
            },
            H: function () { // 24-Hours w/leading 0; 00..23
                return _pad(f.G(), 2);
            },
            i: function () { // Minutes w/leading 0; 00..59
                return _pad(jsdate.getMinutes(), 2);
            },
            s: function () { // Seconds w/leading 0; 00..59
                return _pad(jsdate.getSeconds(), 2);
            },
            u: function () { // Microseconds; 000000-999000
                return _pad(jsdate.getMilliseconds() * 1000, 6);
            },
     
            // Timezone
            e: function () { // Timezone identifier; e.g. Atlantic/Azores, ...
                // The following works, but requires inclusion of the very large
                // timezone_abbreviations_list() function.
    /*              return this.date_default_timezone_get();
    */
                throw 'Not supported (see source code of date() for timezone on how to add support)';
            },
            I: function () { // DST observed?; 0 or 1
                // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
                // If they are not equal, then DST is observed.
                var a = new Date(f.Y(), 0),
                    // Jan 1
                    c = Date.UTC(f.Y(), 0),
                    // Jan 1 UTC
                    b = new Date(f.Y(), 6),
                    // Jul 1
                    d = Date.UTC(f.Y(), 6); // Jul 1 UTC
                return 0 + ((a - c) !== (b - d));
            },
            O: function () { // Difference to GMT in hour format; e.g. +0200
                var a = jsdate.getTimezoneOffset();
                return (a > 0 ? "-" : "+") + _pad(Math.abs(a / 60 * 100), 4);
            },
            P: function () { // Difference to GMT w/colon; e.g. +02:00
                var O = f.O();
                return (O.substr(0, 3) + ":" + O.substr(3, 2));
            },
            T: function () { // Timezone abbreviation; e.g. EST, MDT, ...
                // The following works, but requires inclusion of the very
                // large timezone_abbreviations_list() function.
    /*              var abbr = '', i = 0, os = 0, default = 0;
                if (!tal.length) {
                    tal = that.timezone_abbreviations_list();
                }
                if (that.php_js && that.php_js.default_timezone) {
                    default = that.php_js.default_timezone;
                    for (abbr in tal) {
                        for (i=0; i < tal[abbr].length; i++) {
                            if (tal[abbr][i].timezone_id === default) {
                                return abbr.toUpperCase();
                            }
                        }
                    }
                }
                for (abbr in tal) {
                    for (i = 0; i < tal[abbr].length; i++) {
                        os = -jsdate.getTimezoneOffset() * 60;
                        if (tal[abbr][i].offset === os) {
                            return abbr.toUpperCase();
                        }
                    }
                }
    */
                return 'UTC';
            },
            Z: function () { // Timezone offset in seconds (-43200...50400)
                return -jsdate.getTimezoneOffset() * 60;
            },
     
            // Full Date/Time
            c: function () { // ISO-8601 date.
                return 'Y-m-d\\Th:i:sP'.replace(formatChr, formatChrCb);
            },
            r: function () { // RFC 2822
                return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
            },
            U: function () { // Seconds since UNIX epoch
                return jsdate.getTime() / 1000 | 0;
            }
        };
        this.date = function (format, timestamp) {
            that = this;
            jsdate = ((typeof timestamp === 'undefined') ? new Date() : // Not provided
            (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
            new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
            );
            return format.replace(formatChr, formatChrCb);
        };
        return this.date(format, timestamp);
    }
});

$(document).ready(function(){
    exmDates.init();

    $('input.exmdates_field').change(function(){
        
        var str = $(this).val().split(" ", 3);
        var date = str[0].split("/", 3);
        
        if(str.length>1){
            var time = str[1].split(":",3);
        }
        
        field = $(this).attr('id').replace('exmdate-','');
        eval('var convert = '+field+'_time==2?false:true;');


        if(convert){
            if(time!=undefined && time.length>0)
                valtime = exmDates.mktime(time[0]-7,time[1],time[2]>=0?time[2]:1,date[0],date[1],date[2]);        
            else
                valtime = exmDates.mktime(1,1,1,date[0],date[1],date[2]);
            $("#"+field).val(valtime);
        } else {
            $("#"+field).val($(this).val());
        }

    })
});