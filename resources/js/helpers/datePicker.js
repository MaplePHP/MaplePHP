import { StratoxDom as $ } from '../.././node_modules/stratox/src/StratoxDom.js';
import { MapleDate } from './MapleDate.js';
import { MapleString } from './MapleString.js';

export const datePicker = {
    init: function (settings) {
        datePicker.config = {
            input: ".datepicker",
            placeholder: "--",
            seperator: "/",
            range: false,
            date: true,
            time: false,
            format: "Y-M-D",
            buttonClass: "",
            currentLang: "sv",
            start: false,
            end: false,
            startDate: false,
            dateObj: {
            },
            lang: {
                sv: {
                    choose: "Välj",
                    from: "Från",
                    to: "Till",
                    dayInWeek: Array("Mån", "Tis", "Ons", "Tor", "Fre", "Lör", "Sön"),
                    months: Array("Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September", "Oktober", "November", "December")
                },
                en: {
                    choose: "Choose",
                    from: "From",
                    to: "To",
                    dayInWeek: Array("Mon", "Thu", "Wed", "Thu", "Fri", "Sat", "Sun"),
                    months: Array("January", "February", "March", "April", "May", "June", "Juli", "August", "September", "October", "November", "December")
                }
            },
            open: function () {
            },
            close: function () {
            },
            callback: function () {
            }
        };

        $.extend(datePicker.config, settings);
        datePicker.setup();

        $(document).on("focus", datePicker.config.input, function () {
            
            if (datePicker.current.obj === false) {
                var range, time, range, format, inp, dataDate, start, end, startDate;

                inp = $(this);
                inp.addClass("date-focus");

                dataDate = inp.data("date");

                datePicker.config.range = false;
                datePicker.config.time = false;
                datePicker.config.date = true;

                if (range = inp.data("range")) {
                    datePicker.config.range = parseInt(inp.data("range"));
                }
                if (time = inp.data("time")) {
                    datePicker.config.time = parseInt(inp.data("time"));
                }
                if (format = inp.data("format")) {
                    datePicker.config.format = format;
                }
                if (start = inp.data("start")) {
                    datePicker.config.start = start;
                }
                if (end = inp.data("end")) {
                    datePicker.config.end = end;
                }
                if (startDate = inp.data("start-date")) {
                    datePicker.config.startDate = startDate;
                }
                if (typeof dataDate === "number") {
                    datePicker.config.date = parseInt(dataDate);
                }

                if (datePicker.config.start) {
                    var startArr = datePicker.config.start.split("-");
                    if (startArr.length >= 3 && startArr[0].length === 4 && startArr[1].length === 2 && startArr[2].length === 2) {
                        var createDate = new MapleDate(parseInt(startArr[0]), (parseInt(startArr[1])-1), parseInt(startArr[2]));
                        datePicker.range.start = createDate;

                        if (datePicker.config.startDate && datePicker.config.startDate !== "now") {
                            startArr = datePicker.config.startDate.split("-");
                            datePicker.date = new MapleDate(parseInt(startArr[0]), (parseInt(startArr[1])-1), parseInt(startArr[2]));
                            datePicker.setStartDates();
                        }
                    }
                }

                if (datePicker.config.end) {
                    var startArr = datePicker.config.end.split("-");
                    if (startArr.length >= 3 && startArr[0].length === 4 && startArr[1].length === 2 && startArr[2].length === 2) {
                        datePicker.range.end = new MapleDate(parseInt(startArr[0]), (parseInt(startArr[1])-1), parseInt(startArr[2]));
                    }
                }
                datePicker.current.inp = inp;

                if (!datePicker.config.date) {
                    datePicker.current.range.from = {};
                    $.extend(datePicker.current.range.from, datePicker.today);
                    datePicker.current.range.to = {};
                    $.extend(datePicker.current.range.to, datePicker.today);
                }


                datePicker.append();
                //if(datePicker.config.range) datePicker.append();
                datePicker.current.obj = $(".wa-date-picker");

                datePicker.current.inp.blur();
                datePicker.current.obj.find(".wa-date-picked-btn").first().focus();

                datePicker.current.obj.keydown(function (e) {
                    if (e.which === 27) {
                        //datePicker.current.inp.focus();
                        datePicker.remove();
                    }

                });
            }
        });

        $(document).on("click", ".wa-date-month-nav", function (e) {
            e.preventDefault();
            var myClick = $(this);
            if (myClick.hasClass("prev")) {
                datePicker.prevMonth();
            } else {
                datePicker.nextMonth();
            }
            datePicker.getDaysInMonth(datePicker.current.year, datePicker.current.month);
            datePicker.replace();
        });

        $(document).on("click", ".wa-date-today-btn", function (e) {
            e.preventDefault();
            datePicker.reset();
        });

        var value = "";
        $(document).on("click", ".wa-date-picked-btn", function (e) {
            e.preventDefault();
            var myClick = $(this);
            datePicker.current.day = myClick.data("day");

            datePicker.current.select = true;

            if (datePicker.config.range) {
                if (typeof datePicker.current.range.from === "object") {
                    if (datePicker.validStartRange(datePicker.current)) {
                        datePicker.current.range.to = {};
                        $.extend(datePicker.current.range.to, datePicker.current);
                        datePicker.replace();
                    }
                } else {
                    datePicker.current.range.from = {};
                    $.extend(datePicker.current.range.from, datePicker.current);
                    
                    datePicker.current.range.to = {};
                    $.extend(datePicker.current.range.to, datePicker.current);
                    
                    datePicker.replace();
                }
            } else {
                datePicker.replace();
            }

        });

        $(document).on("change", ".wa-date-time-inp", function () {
            var inp = $(this), val = parseInt(inp.val()), valFor;

            if (isNaN(val)) {
                inp.val("00");
            } else {
                valFor = val;
                //if(!datePicker.current.range.to) datePicker.current.range.to = {};
                //if(!datePicker.current.range.from) datePicker.current.range.from = {};
                if (datePicker.config.range) {
                    if (inp.hasClass("fhour")) {
                        datePicker.current.range.from.hour = valFor;
                    }
                    if (inp.hasClass("fminute")) {
                        datePicker.current.range.from.minute = valFor;
                    }

                    if (inp.hasClass("thour")) {
                        datePicker.current.range.to.hour = valFor;
                    }
                    if (inp.hasClass("tminute")) {
                        datePicker.current.range.to.minute = valFor;
                    }
                } else {
                    if (inp.hasClass("fhour")) {
                        datePicker.current.hour = valFor;
                    }
                    if (inp.hasClass("fminute")) {
                        datePicker.current.minute = valFor;
                    }
                }
            }
        });

        $(document).on("click", "#wa-date-insert-btn", function (e) {
            e.preventDefault();

            let timeSpace = "", value = {
                start: {
                    date: null,
                    time: null
                },
                end: {
                    date: null,
                    time: null
                }
            };


            if (datePicker.config.range) {
                if (datePicker.validRange()) {
                    value.start.date = datePicker.value(datePicker.current.range.from);
                    value.start.time = datePicker.timeValue(datePicker.current.range.from);
                    value.end.date = datePicker.value(datePicker.current.range.to);
                    value.end.time = datePicker.timeValue(datePicker.current.range.to);

                    if (value.start.time || value.end.time) {
                        timeSpace = " ";
                    }
                    datePicker.current.inp.val(value.start.date+timeSpace+value.start.time+" - "+value.end.date+timeSpace+value.end.time);
                } else {
                    datePicker.current.obj.find(".wa-date-calendar").addClass("range-error");
                    return false;
                }
            } else {
                value.start.date = $.trim(datePicker.value(datePicker.current));
                value.start.time = datePicker.timeValue(datePicker.current);
                if (value.start.time) {
                    timeSpace = " ";
                }
                value.end = null;

                datePicker.current.inp.val(value.start.date+timeSpace+value.start.time);
                datePicker.config.dateObj.hour = datePicker.current.hour;
                datePicker.config.dateObj.minute = datePicker.current.minute;
            }

            //datePicker.current.inp.trigger("change");
            //datePicker.current.inp.focus();


            datePicker.config.callback(value);
            datePicker.remove();
        });

        $(document).on("click", ".invalid-range", function (e) {
            e.preventDefault();
            datePicker.rangeView();
        });

        $(document).on("click", ".wa-date-picker-bg", function (e) {
            e.preventDefault();
            var myClick = $(this);
            datePicker.remove();
        });

    }, setup: function () {
        datePicker.setStartDates();
        datePicker.range = {
            start: false,
            end: false
        };

    }, setStartDates: function () {
        if (typeof datePicker.date !== "object") {
            datePicker.date = new MapleDate();
        }
        var date = datePicker.date;

        datePicker.today = {
            day: date.getDate(),
            month: date.getMonth(),
            year: date.getFullYear(),
            hour: date.getHours(),
            minute: date.getMinutes(),
            date: date
        };

        datePicker.current = {
            day: date.getDate(),
            dayTo: false,
            month: date.getMonth(),
            year: date.getFullYear(), // 0-11
            hour: 0,
            minute: 0,
            //hour: date.getHours(),
            //minute: date.getMinutes(),
            dayOfweek: datePicker.startMonday(date.getDay()),
            feed: Array(),
            obj: false,
            inp: false,
            select: false,
            range: {
                from: false,
                to: false
            },
        };

        $.extend(datePicker.current, datePicker.config.dateObj);
        datePicker.getDaysInMonth(datePicker.current.year, datePicker.current.month);


    }, resetDates: function () {
        var date = new MapleDate();
        datePicker.current.day = date.getDate();
        datePicker.current.month = date.getMonth();
        datePicker.current.year = date.getFullYear();
        datePicker.current.hour = 0;
        datePicker.current.minute = 0;
        //datePicker.current.hour = date.getHours();
        //datePicker.current.minute = date.getMinutes();
        datePicker.current.dayOfweek = datePicker.startMonday(date.getDay());
        //datePicker.resetRange();
        datePicker.getDaysInMonth(datePicker.current.year, datePicker.current.month);

    }, resetRange: function () {
        if (!datePicker.config.date) {
            datePicker.current.range.from = datePicker.today;
            datePicker.current.range.to = datePicker.today;
        } else {
            datePicker.current.range = {
                from: false,
                to: false
            };
        }


    }, validRange: function () {
        if (datePicker.current.range.from && datePicker.current.range.to) {
            var c1 = new MapleDate(datePicker.current.range.from.year, datePicker.current.range.from.month, datePicker.current.range.from.day, datePicker.current.range.from.hour, datePicker.current.range.from.minute);
            var c2 = new MapleDate(datePicker.current.range.to.year, datePicker.current.range.to.month, datePicker.current.range.to.day, datePicker.current.range.to.hour, datePicker.current.range.to.minute);
            return (c1 <= c2);
        }
        return false;

    }, validStartRange: function (current) {
        if (datePicker.current.range.from) {
            var c1 = new MapleDate(datePicker.current.range.from.year, datePicker.current.range.from.month, datePicker.current.range.from.day);
            var c2 = new MapleDate(current.year, current.month, current.day);
            return (c1 < c2);
        }
        return true;
    }, eqCurrent: function (current) {

        if (datePicker.current.select === false) {
            return false;
        }

        var c1 = new MapleDate(datePicker.current.year, datePicker.current.month, datePicker.current.day);
        var c2 = new MapleDate(current.year, current.month, current.day);
        return (c1 >= c2 && c1 <= c2);

    }, eqRange: function (current) {
        if (datePicker.current.range.from) {
            var c1 = new MapleDate(datePicker.current.range.from.year, datePicker.current.range.from.month, datePicker.current.range.from.day);
            var c2 = new MapleDate(current.year, current.month, current.day);
            return (c1 >= c2 && c1 <= c2);
        }
        return false;

    }, betweenRange: function (A, B, current) {
        var C = new MapleDate(current.year, current.month, current.day);
        return (C >= A && C <= B);

    }, eqRangeTo: function (current) {
        if (datePicker.current.range.from && datePicker.current.range.to) {
            var c1 = new MapleDate(datePicker.current.range.to.year, datePicker.current.range.to.month, datePicker.current.range.to.day);
            var c2 = new MapleDate(current.year, current.month, current.day);


            return (c1 >= c2);
        }
        return false;

    }, isToday: function (current) {
        var c1 = new MapleDate(datePicker.today.year, datePicker.today.month, datePicker.today.day);
        var c2 = new MapleDate(current.year, current.month, current.day);

        return (c1 >= c2 && c1 <= c2);

    }, getDaysInMonth: function (year, month) {
        var days = Array(),
        date = new MapleDate(year, month, 1),
        day = date.getDay();

        datePicker.current.feed = Array();

        while (date.getMonth() === month) {
            var nd = new MapleDate(date);
            datePicker.current.feed.push({
                day: nd.getDate(),
                month: nd.getMonth(),
                year: nd.getFullYear(),
                dayOfweek: datePicker.startMonday(nd.getDay()),
                week: nd.getWeek()
            });
            date.setDate(date.getDate()+1);
        }

    }, nextMonth: function () {
        datePicker.current.month += 1;
        if (datePicker.current.month > 11) {
            datePicker.current.month = 0;
            datePicker.current.year += 1;
        }

        if (!datePicker.current.range.from) {
            datePicker.current.select = false;
        }

    }, prevMonth: function () {
        datePicker.current.month -= 1;

        if (datePicker.current.month < 0) {
            datePicker.current.month = 11;
            datePicker.current.year -= 1;
        }

        if (!datePicker.current.range.from) {
            datePicker.current.select = false;
        }

    }, startMonday: function (day) {
        return (day === 0 ? 6 : day-1);

    }, toRealDayInWeekNum: function (day) {
        return (day === 0 ? 1 : day-1);

    }, create: function () {
        var count = 0, index = 0, html = '';

        html += '<div class="wa-date-picker abs'+(datePicker.config.range ? " has-range" : "")+'">';
        html += datePicker.createCalendar();
        html += '<div class="wa-date-picker-bg abs"></div>';
        html += '</div>';

        return html;

    }, createCalendar: function () {

        var count = 0, index = 0, weekIndex = 0, html = '',
        totalRow = (datePicker.current.feed.length > 30 && datePicker.current.feed[0].dayOfweek >= 5) ? 6 : 5;

        html += '<div class="wa-date-calendar abs middle">';
        if (datePicker.config.date) {
            html += '<div class="wa-date-header relative legend center">';
            html += '<a href="#" class="wa-date-month-nav prev abs left top"><svg width="28" height="74" viewBox="0 0 28 74" xmlns="http://www.w3.org/2000/svg"><path d="M27.144 73L2 37.036 27.144 1.483" stroke="#4A4A4A" fill="none" fill-rule="evenodd"></path></svg></a>';
            html += '<a href="#" class="wa-date-today-btn">'+datePicker.toText('months', datePicker.current.month)+' '+datePicker.current.year+'</a>';
            html += '<a href="#" class="wa-date-month-nav next abs right top"><svg width="28" height="74" viewBox="0 0 28 74" xmlns="http://www.w3.org/2000/svg"><path d="M.856 73L26 37.036.856 1.483" stroke="#4A4A4A" fill="none" fill-rule="evenodd"></path></svg></a>';
            html += '</div>';

            html += '<table>';
            html += '<tr>';
            html += '<th class="date-weekday date-week-h"><span>V</span></th>';
            for (var d = 0; d < 7; d++) {
                html += '<th class="date-weekday"><span>'+datePicker.toText('dayInWeek', d)+'</span></th>';
            }
            html += '</tr>';

            for (var r = 0; r < totalRow; r++) {
                html += '<tr>';
                for (var c = 0; c < 8; c++) {
                    if (c > 0) {
                        if (count >= datePicker.current.feed[0].dayOfweek) {
                            if (datePicker.current.feed[index]) {
                                var current = datePicker.current.feed[index], today = (datePicker.isToday(current) ? " today" : ""), active = "";
                                if (datePicker.config.range && !datePicker.validStartRange(current)) {
                                    active = (datePicker.eqRange(current)) ? " active" : "";
                                    html += '<td class="date-day invalid-range'+today+active+'"><span>'+current.day+'</span></td>';
                                } else {
                                    if (datePicker.config.range) {
                                        active = (datePicker.eqRangeTo(current)) ? " active" : "";
                                    } else {
                                        active = (datePicker.eqCurrent(current)) ? " active" : "";
                                    }

                                    if ((datePicker.range.start && datePicker.range.end && !datePicker.betweenRange(datePicker.range.start, datePicker.range.end, current)) ||
                                        (!datePicker.range.start && datePicker.range.end && !datePicker.betweenRange(datePicker.range.start, datePicker.range.end, current))) {
                                        html += '<td class="date-day date-disabled invalid-range'+today+'"><span>'+current.day+'</span></td>';
                                    } else {
                                        html += '<td class="date-day'+today+active+'"><a class="wa-date-picked-btn" href="#" data-value="'+datePicker.value(current)+'" data-day="'+current.day+'">'+current.day+'</a></td>';
                                    }
                                }
                                index++;
                            } else {
                                html += '<td class="date-empty"><span>&nbsp;</span></td>';
                            }
                        } else {
                            html += '<td class="date-empty"><span>&nbsp;</span></td>';
                        }
                        count++;
                    } else {
                        var current = datePicker.current.feed[weekIndex];
                        if (datePicker.current.feed[weekIndex].dayOfweek > 0) {
                            html += '<td class="date-weekday date-week"></td>';
                            weekIndex += (7-datePicker.current.feed[weekIndex].dayOfweek);
                        } else {
                            html += '<td class="date-weekday date-week"><span>v'+current.week+'</span></td>';
                            weekIndex += 7;
                        }
                    }
                }
                html += '</tr>';
            }
            html += '</table>';
        }

        var fromTimeInp = '<input id="from-hour" class="wa-date-time-inp fhour" name="fhour" value="'+datePicker.getHour(datePicker.current)+'">:<input id="from-minute" class="wa-date-time-inp fminute" name="fminute" value="'+datePicker.getMinute(datePicker.current)+'">',
        toTimeInp = '<input id="to-hour" class="wa-date-time-inp thour" name="thour" value="'+datePicker.getHour(datePicker.current)+'">:<input id="to-minute" class="wa-date-time-inp tminute" name="tminute" value="'+datePicker.getMinute(datePicker.current)+'">';

        if (datePicker.config.range) {
            var timeClass = "wa-date-time";
            if (datePicker.config.date) {
                timeClass = "wa-date-time float-right";
            }

            html += '<div class="wa-date-range clearfix legend from'+(datePicker.current.range.from ? " active" : "")+'">';
            if (datePicker.config.time) {
                html += '<div class="'+timeClass+'">'+fromTimeInp+'</div>';
            }
            if (datePicker.config.date) {
                html += '<div class="overflow"><strong>'+datePicker.lang("from")+':</strong> <span>'+datePicker.value(datePicker.current.range.from)+'</span></div>';
            }
            html += '</div>';

            html += '<div class="wa-date-range clearfix legend till'+(datePicker.current.range.to ? " active" : "")+'">';
            if (datePicker.config.time) {
                html += '<div class="'+timeClass+'">'+toTimeInp+'</div>';
            }
            if (datePicker.config.date) {
                html += '<div class="overflow"><strong>'+datePicker.lang("to")+':</strong> <span>'+datePicker.value(datePicker.current.range.to)+'</span></div>';
            }
            html += '</div>';
        } else {
            if (datePicker.config.time) {
                html += '<div class="wa-date-range legend">'+fromTimeInp+'</div>';
            }
        }

        html += '<a id="wa-date-insert-btn" class="wa-date-btn block button v2 '+datePicker.config.buttonClass+'" href="#">'+datePicker.lang("choose")+'</a>';
        html += '</div>';
        return html;

    }, append: function () {
        $("body").append(datePicker.create());

    }, replace: function () {
        datePicker.current.obj.find(".wa-date-calendar").replaceWith(datePicker.createCalendar());

    }, reset: function () {
        datePicker.resetDates();
        datePicker.current.obj.find(".wa-date-calendar").replaceWith(datePicker.createCalendar());

    }, rangeView: function () {
        datePicker.resetRange();
        datePicker.current.obj.find(".wa-date-calendar").replaceWith(datePicker.createCalendar());

    }, remove: function () {
        datePicker.current.obj.remove();
        datePicker.current.inp.removeClass("date-focus");
        datePicker.setup();
        
    }, toText: function (key, int) {
        var lang = datePicker.lang();
        return (typeof lang[key][int] === "string") ? lang[key][int] : "";

    }, getMonth: function (date) {
        let str = new MapleString(date.month+1);
        return str.padStart(2, '0');

    }, getDay: function (date) {
        let str = new MapleString(date.day);
        return str.padStart(2, '0');

    }, getHour: function (date) {
        let str = new MapleString(date.hour);
        return str.padStart(2, '0');

    }, getMinute: function (date) {
        let str = new MapleString(date.minute);
        return str.padStart(2, '0');

    }, value: function (date) {
        if (datePicker.config.date) {
            var toArr = Array();
            if (date.year && (typeof date.month === "number") && date.day) {
                if (datePicker.config.format.indexOf("Y") >= 0) {
                    toArr.push(date.year);
                }
                if (datePicker.config.format.indexOf("M") >= 0) {
                    toArr.push(datePicker.getMonth(date));
                }
                if (datePicker.config.format.indexOf("D") >= 0) {
                    toArr.push(datePicker.getDay(date));
                }
                return toArr.join(datePicker.config.seperator);
            }
            return datePicker.config.placeholder+datePicker.config.seperator+datePicker.config.placeholder+datePicker.config.seperator+datePicker.config.placeholder;
        }
        return "";

    }, timeValue: function (date) {
        if (datePicker.config.time) {
            if ((typeof date.hour === "number") && (typeof date.minute === "number")) {
                return ""+datePicker.getHour(date)+":"+datePicker.getMinute(date);
            }
            return ""+datePicker.config.placeholder+":"+datePicker.config.placeholder+"";
        }
        return "";
    
    }, lang: function (key) {
        if (typeof key === "string") {
            return datePicker.config.lang[datePicker.config.currentLang][key];
        }

        return datePicker.config.lang[datePicker.config.currentLang];
    }

};