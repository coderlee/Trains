(function (e, t) {
    var i = document,
        n = window;
    var l = i.documentElement;
    var r, a;
    var d, o = document.createElement("style");
    var s;
    function m() {
        var i = l.getBoundingClientRect().width;
        if (!t) {
            t = 540
        }
        if (i > t) {
            i = t
        }
        var n = i * 100 / e;
        o.innerHTML = "html{font-size:" + n + "px;}"
    }
    r = i.querySelector('meta[name="viewport"]');
    a = "width=device-width,initial-scale=1,maximum-scale=1.0,user-scalable=no,viewport-fit=cover";
    if (r) {
        r.setAttribute("content", a)
    } else {
        r = i.createElement("meta");
        r.setAttribute("name", "viewport");
        r.setAttribute("content", a);
        if (l.firstElementChild) {
            l.firstElementChild.appendChild(r)
        } else {
            var c = i.createElement("div");
            c.appendChild(r);
            i.write(c.innerHTML);
            c = null
        }
    }
    m();
    if (l.firstElementChild) {
        l.firstElementChild.appendChild(o)
    } else {
        var c = i.createElement("div");
        c.appendChild(o);
        i.write(c.innerHTML);
        c = null
    }
    n.addEventListener("resize", function () {
        clearTimeout(s);
        s = setTimeout(m, 300)
    }, false);
    n.addEventListener("pageshow", function (e) {
        if (e.persisted) {
            clearTimeout(s);
            s = setTimeout(m, 300)
        }
    }, false);
    if (i.readyState === "complete") {
        i.body.style.fontSize = "16px"
    } else {
        i.addEventListener("DOMContentLoaded", function (e) {
            i.body.style.fontSize = "16px"
        }, false)
    }
})(750, 750);