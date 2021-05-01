/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/layout/dist/js/app.f0c95102.js":
/*!**************************************************!*\
  !*** ./resources/layout/dist/js/app.f0c95102.js ***!
  \**************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./resources/layout/node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);


function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

(function (t) {
  function e(e) {
    for (var n, o, i = e[0], u = e[1], c = e[2], d = 0, p = []; d < i.length; d++) {
      o = i[d], Object.prototype.hasOwnProperty.call(s, o) && s[o] && p.push(s[o][0]), s[o] = 0;
    }

    for (n in u) {
      Object.prototype.hasOwnProperty.call(u, n) && (t[n] = u[n]);
    }

    l && l(e);

    while (p.length) {
      p.shift()();
    }

    return a.push.apply(a, c || []), r();
  }

  function r() {
    for (var t, e = 0; e < a.length; e++) {
      for (var r = a[e], n = !0, i = 1; i < r.length; i++) {
        var u = r[i];
        0 !== s[u] && (n = !1);
      }

      n && (a.splice(e--, 1), t = o(o.s = r[0]));
    }

    return t;
  }

  var n = {},
      s = {
    app: 0
  },
      a = [];

  function o(e) {
    if (n[e]) return n[e].exports;
    var r = n[e] = {
      i: e,
      l: !1,
      exports: {}
    };
    return t[e].call(r.exports, r, r.exports, o), r.l = !0, r.exports;
  }

  o.m = t, o.c = n, o.d = function (t, e, r) {
    o.o(t, e) || Object.defineProperty(t, e, {
      enumerable: !0,
      get: r
    });
  }, o.r = function (t) {
    "undefined" !== typeof Symbol && Symbol.toStringTag && Object.defineProperty(t, Symbol.toStringTag, {
      value: "Module"
    }), Object.defineProperty(t, "__esModule", {
      value: !0
    });
  }, o.t = function (t, e) {
    if (1 & e && (t = o(t)), 8 & e) return t;
    if (4 & e && "object" === _typeof(t) && t && t.__esModule) return t;
    var r = Object.create(null);
    if (o.r(r), Object.defineProperty(r, "default", {
      enumerable: !0,
      value: t
    }), 2 & e && "string" != typeof t) for (var n in t) {
      o.d(r, n, function (e) {
        return t[e];
      }.bind(null, n));
    }
    return r;
  }, o.n = function (t) {
    var e = t && t.__esModule ? function () {
      return t["default"];
    } : function () {
      return t;
    };
    return o.d(e, "a", e), e;
  }, o.o = function (t, e) {
    return Object.prototype.hasOwnProperty.call(t, e);
  }, o.p = "/";
  var i = window["webpackJsonp"] = window["webpackJsonp"] || [],
      u = i.push.bind(i);
  i.push = e, i = i.slice();

  for (var c = 0; c < i.length; c++) {
    e(i[c]);
  }

  var l = u;
  a.push([0, "chunk-vendors"]), r();
})({
  0: function _(t, e, r) {
    t.exports = r("cd49");
  },
  "2f36": function f36(t, e, r) {
    "use strict";

    r("7cc0");
  },
  "4f0e": function f0e(t, e, r) {
    "use strict";

    r("cb80");
  },
  "75e7": function e7(t, e, r) {},
  "7cc0": function cc0(t, e, r) {},
  b2a5: function b2a5(t, e, r) {
    "use strict";

    r("75e7");
  },
  c3d4: function c3d4(t, e, r) {},
  c44d: function c44d(t, e, r) {
    "use strict";

    r("c3d4");
  },
  cb80: function cb80(t, e, r) {},
  cd49: function cd49(t, e, r) {
    "use strict";

    r.r(e);
    r("e260"), r("e6cf"), r("cca6"), r("a79d");

    var n = r("2b0e"),
        s = function s() {
      var t = this,
          e = t.$createElement,
          n = t._self._c || e;
      return n("div", {
        attrs: {
          id: "app"
        }
      }, [n("img", {
        attrs: {
          alt: "Vue logo",
          src: r("cf05")
        }
      }), n("HelloWorld", {
        attrs: {
          msg: "Welcome to Your Vue.js + TypeScript App"
        }
      })], 1);
    },
        a = [],
        o = r("d4ec"),
        i = r("262e"),
        u = r("2caf"),
        c = r("9ab4"),
        l = r("1b40"),
        d = function d() {
      var t = this,
          e = t.$createElement,
          r = t._self._c || e;
      return r("div", {
        staticClass: "hello"
      }, [r("h1", [t._v(t._s(t.msg))]), t._m(0), r("h3", [t._v("Installed CLI Plugins")]), t._m(1), r("h3", [t._v("Essential Links")]), t._m(2), r("h3", [t._v("Ecosystem")]), t._m(3)]);
    },
        p = [function () {
      var t = this,
          e = t.$createElement,
          r = t._self._c || e;
      return r("p", [t._v(" For a guide and recipes on how to configure / customize this project,"), r("br"), t._v(" check out the "), r("a", {
        attrs: {
          href: "https://cli.vuejs.org",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("vue-cli documentation")]), t._v(". ")]);
    }, function () {
      var t = this,
          e = t.$createElement,
          r = t._self._c || e;
      return r("ul", [r("li", [r("a", {
        attrs: {
          href: "https://github.com/vuejs/vue-cli/tree/dev/packages/%40vue/cli-plugin-babel",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("babel")])]), r("li", [r("a", {
        attrs: {
          href: "https://github.com/vuejs/vue-cli/tree/dev/packages/%40vue/cli-plugin-typescript",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("typescript")])]), r("li", [r("a", {
        attrs: {
          href: "https://github.com/vuejs/vue-cli/tree/dev/packages/%40vue/cli-plugin-vuex",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("vuex")])]), r("li", [r("a", {
        attrs: {
          href: "https://github.com/vuejs/vue-cli/tree/dev/packages/%40vue/cli-plugin-eslint",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("eslint")])]), r("li", [r("a", {
        attrs: {
          href: "https://github.com/vuejs/vue-cli/tree/dev/packages/%40vue/cli-plugin-unit-jest",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("unit-jest")])])]);
    }, function () {
      var t = this,
          e = t.$createElement,
          r = t._self._c || e;
      return r("ul", [r("li", [r("a", {
        attrs: {
          href: "https://vuejs.org",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("Core Docs")])]), r("li", [r("a", {
        attrs: {
          href: "https://forum.vuejs.org",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("Forum")])]), r("li", [r("a", {
        attrs: {
          href: "https://chat.vuejs.org",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("Community Chat")])]), r("li", [r("a", {
        attrs: {
          href: "https://twitter.com/vuejs",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("Twitter")])]), r("li", [r("a", {
        attrs: {
          href: "https://news.vuejs.org",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("News")])])]);
    }, function () {
      var t = this,
          e = t.$createElement,
          r = t._self._c || e;
      return r("ul", [r("li", [r("a", {
        attrs: {
          href: "https://router.vuejs.org",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("vue-router")])]), r("li", [r("a", {
        attrs: {
          href: "https://vuex.vuejs.org",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("vuex")])]), r("li", [r("a", {
        attrs: {
          href: "https://github.com/vuejs/vue-devtools#vue-devtools",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("vue-devtools")])]), r("li", [r("a", {
        attrs: {
          href: "https://vue-loader.vuejs.org",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("vue-loader")])]), r("li", [r("a", {
        attrs: {
          href: "https://github.com/vuejs/awesome-vue",
          target: "_blank",
          rel: "noopener"
        }
      }, [t._v("awesome-vue")])])]);
    }],
        v = function (t) {
      Object(i["a"])(r, t);
      var e = Object(u["a"])(r);

      function r() {
        return Object(o["a"])(this, r), e.apply(this, arguments);
      }

      return r;
    }(l["c"]);

    Object(c["a"])([Object(l["b"])()], v.prototype, "msg", void 0), v = Object(c["a"])([l["a"]], v);

    var h = v,
        m = h,
        f = (r("b2a5"), r("2877")),
        b = Object(f["a"])(m, d, p, !1, null, "34db786d", null),
        g = b.exports,
        _ = function (t) {
      Object(i["a"])(r, t);
      var e = Object(u["a"])(r);

      function r() {
        return Object(o["a"])(this, r), e.apply(this, arguments);
      }

      return r;
    }(l["c"]);

    _ = Object(c["a"])([Object(l["a"])({
      components: {
        HelloWorld: g
      }
    })], _);

    var w = _,
        y = w,
        j = (r("2f36"), Object(f["a"])(y, s, a, !1, null, "553d7f44", null)),
        S = j.exports,
        k = function k() {
      var t = this,
          e = t.$createElement,
          r = t._self._c || e;
      return r("div", {
        staticClass: "v-setsList"
      }, [r("a", {
        staticClass: "btn btn-success",
        attrs: {
          id: "create__button",
          href: t.addSetLink
        }
      }, [t._v("Stwórz")]), r("table", {
        staticClass: "table"
      }, [t._m(0), r("tbody", t._l(t.sets, function (e, n) {
        return r("tr", {
          key: n
        }, [r("td", [t._v(t._s(n))]), r("td", [t._v(" " + t._s(e.set[0].name) + " ")]), r("td", [t._v(" " + t._s(e.set[0].number) + " ")]), r("td", [t._v(" " + t._s(e.set[0].stock) + " ")]), r("td", [r("ul", t._l(e.products, function (e) {
          return r("li", {
            key: e.id
          }, [r("b", [t._v(t._s(e.symbol))]), t._v(" => " + t._s(e.name) + " "), r("b", [t._v("Ilość: " + t._s(e.stock))])]);
        }), 0)]), r("td", [r("div", {
          staticClass: "form-group"
        }, [r("label", [t._v("Ilość zestawów")]), r("input", {
          directives: [{
            name: "model",
            rawName: "v-model",
            value: t.completingSet[n],
            expression: "completingSet[index]"
          }],
          staticClass: "form-control",
          attrs: {
            type: "number",
            name: "number",
            min: "1"
          },
          domProps: {
            value: t.completingSet[n]
          },
          on: {
            input: function input(e) {
              e.target.composing || t.$set(t.completingSet, n, e.target.value);
            }
          }
        })]), r("button", {
          staticClass: "btn btn-sm btn-primary",
          attrs: {
            type: "submit"
          }
        }, [r("span", {
          staticClass: "hidden-xs hidden-sm",
          on: {
            click: function click(e) {
              return t.completing(n, t.completingSet[n]);
            }
          }
        }, [t._v("Stwórz")])])]), r("td", [r("div", {
          staticClass: "form-group"
        }, [r("label", [t._v("Ilość zestawów")]), r("input", {
          directives: [{
            name: "model",
            rawName: "v-model",
            value: t.disassemblySet[n],
            expression: "disassemblySet[index]"
          }],
          staticClass: "form-control",
          attrs: {
            type: "number",
            name: "number",
            min: "1"
          },
          domProps: {
            value: t.disassemblySet[n]
          },
          on: {
            input: function input(e) {
              e.target.composing || t.$set(t.disassemblySet, n, e.target.value);
            }
          }
        })]), r("button", {
          staticClass: "btn btn-sm btn-primary",
          attrs: {
            type: "submit"
          }
        }, [r("span", {
          staticClass: "hidden-xs hidden-sm",
          on: {
            click: function click(e) {
              return t.disassembly(n, t.disassemblySet[n]);
            }
          }
        }, [t._v("Zdekompletuj")])])]), r("td", [r("a", {
          staticClass: "btn btn-sm btn-primary",
          attrs: {
            href: t.getSetEditLink(n)
          }
        }, [r("i", {
          staticClass: "voyager-trash"
        }), r("span", {
          staticClass: "hidden-xs hidden-sm"
        }, [t._v("Edytuj")])]), r("button", {
          staticClass: "btn btn-sm btn-danger",
          attrs: {
            type: "submit"
          },
          on: {
            click: function click(e) {
              return t.deleteSet(n);
            }
          }
        }, [r("i", {
          staticClass: "voyager-trash"
        }), r("span", {
          staticClass: "hidden-xs hidden-sm"
        }, [t._v("Usuń")])])])]);
      }), 0)])]);
    },
        O = [function () {
      var t = this,
          e = t.$createElement,
          r = t._self._c || e;
      return r("thead", [r("tr", [r("th", [t._v("Id")]), r("th", [t._v("Nazwa zestawu")]), r("th", [t._v("Numer wewnętrzny zestawu")]), r("th", [t._v("Ilość zestawów")]), r("th", [t._v("Lista produktów w zestawie")]), r("th", [t._v("Stwórz podaną ilość zestawów")]), r("th", [t._v("Zdekompletuj podaną liczbę zestawów")]), r("th", [t._v("Akcje")])])]);
    }],
        x = r("1da1"),
        R = r("bee2");

    r("96cf");

    function C(t) {
      return window.location.protocol + "//" + window.location.hostname + ":8000/" + t;
    }

    var E = function (t) {
      Object(i["a"])(r, t);
      var e = Object(u["a"])(r);

      function r() {
        var t;
        return Object(o["a"])(this, r), t = e.apply(this, arguments), t.completingSet = [], t.disassemblySet = [], t;
      }

      return Object(R["a"])(r, [{
        key: "sets",
        get: function get() {
          var t;
          return null === (t = this.$store) || void 0 === t ? void 0 : t.getters["SetsService/sets"];
        }
      }, {
        key: "getSetEditLink",
        value: function value(t) {
          return C("admin/products/sets/" + t + "/edytuj");
        }
      }, {
        key: "addSetLink",
        get: function get() {
          return C("/admin/products/sets/nowy");
        }
      }, {
        key: "mounted",
        value: function () {
          var t = Object(x["a"])(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function t() {
            var e;
            return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function (t) {
              while (1) {
                switch (t.prev = t.next) {
                  case 0:
                    return t.next = 2, null === (e = this.$store) || void 0 === e ? void 0 : e.dispatch("SetsService/loadSets");

                  case 2:
                  case "end":
                    return t.stop();
                }
              }
            }, t, this);
          }));

          function e() {
            return t.apply(this, arguments);
          }

          return e;
        }()
      }, {
        key: "completing",
        value: function () {
          var t = Object(x["a"])(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function t(e, r) {
            var n;
            return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function (t) {
              while (1) {
                switch (t.prev = t.next) {
                  case 0:
                    return console.log("Zestaw: " + e + "  sztuk: " + r), t.next = 3, this.$store.dispatch("SetsService/completing", {
                      setId: e,
                      count: r
                    });

                  case 3:
                    return t.next = 5, null === (n = this.$store) || void 0 === n ? void 0 : n.dispatch("SetsService/loadSets");

                  case 5:
                  case "end":
                    return t.stop();
                }
              }
            }, t, this);
          }));

          function e(e, r) {
            return t.apply(this, arguments);
          }

          return e;
        }()
      }, {
        key: "disassembly",
        value: function () {
          var t = Object(x["a"])(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function t(e, r) {
            var n;
            return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function (t) {
              while (1) {
                switch (t.prev = t.next) {
                  case 0:
                    return console.log("Zestaw: " + e + "  sztuk: " + r), t.next = 3, this.$store.dispatch("SetsService/disassembly", {
                      setId: e,
                      count: r
                    });

                  case 3:
                    return t.next = 5, null === (n = this.$store) || void 0 === n ? void 0 : n.dispatch("SetsService/loadSets");

                  case 5:
                  case "end":
                    return t.stop();
                }
              }
            }, t, this);
          }));

          function e(e, r) {
            return t.apply(this, arguments);
          }

          return e;
        }()
      }, {
        key: "deleteSet",
        value: function () {
          var t = Object(x["a"])(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function t(e) {
            var r;
            return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function (t) {
              while (1) {
                switch (t.prev = t.next) {
                  case 0:
                    return console.log("Zestaw: " + e), t.next = 3, this.$store.dispatch("SetsService/delete", e);

                  case 3:
                    return t.next = 5, null === (r = this.$store) || void 0 === r ? void 0 : r.dispatch("SetsService/loadSets");

                  case 5:
                  case "end":
                    return t.stop();
                }
              }
            }, t, this);
          }));

          function e(e) {
            return t.apply(this, arguments);
          }

          return e;
        }()
      }]), r;
    }(l["c"]);

    E = Object(c["a"])([Object(l["a"])({
      components: {}
    })], E);

    var T = E,
        $ = T,
        P = (r("c44d"), Object(f["a"])($, k, O, !1, null, "0d6391b3", null)),
        L = P.exports,
        z = function z() {
      var t = this,
          e = t.$createElement,
          r = t._self._c || e;
      return r("div", {
        staticClass: "v-setEdit"
      }, [r("div", {
        staticClass: "form-group"
      }, [r("label", [t._v("Wyszukaj produkct aby dodać do zestawu")]), r("input", {
        directives: [{
          name: "model",
          rawName: "v-model",
          value: t.word,
          expression: "word"
        }],
        staticClass: "form-control",
        attrs: {
          type: "text"
        },
        domProps: {
          value: t.word
        },
        on: {
          keyup: function keyup(e) {
            return t.searchProducts();
          },
          input: function input(e) {
            e.target.composing || (t.word = e.target.value);
          }
        }
      })]), r("table", {
        staticClass: "table table-hover",
        attrs: {
          id: "dataTable"
        }
      }, [t._m(0), t.products.length > 0 ? r("tbody", {
        attrs: {
          id: "productTable"
        }
      }, t._l(t.products, function (e, n) {
        return r("tr", {
          key: n
        }, [r("td"), r("td", [t._v(t._s(n))]), r("td", [t._v(t._s(e.name))]), r("td", [t._v(t._s(e.symbol))]), r("td", [t._v(t._s(e.manufacturer))]), r("td")]);
      }), 0) : t._e()])]);
    },
        I = [function () {
      var t = this,
          e = t.$createElement,
          r = t._self._c || e;
      return r("thead", [r("tr", [r("th"), r("th", [t._v("ID")]), r("th", [t._v("Nazwa produktu")]), r("th", [t._v("Symbol")]), r("th", [t._v("Producent")]), r("th", [t._v("Akcja")])])]);
    }],
        H = function (t) {
      Object(i["a"])(r, t);
      var e = Object(u["a"])(r);

      function r() {
        var t;
        return Object(o["a"])(this, r), t = e.apply(this, arguments), t.word = "", t.searchParams = {
          name: "",
          symbol: "",
          manufacturer: "",
          word: ""
        }, t;
      }

      return Object(R["a"])(r, [{
        key: "products",
        get: function get() {
          var t;
          return null === (t = this.$store) || void 0 === t ? void 0 : t.getters["SetsService/products"];
        }
      }, {
        key: "searchProducts",
        value: function () {
          var t = Object(x["a"])(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function t() {
            return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function (t) {
              while (1) {
                switch (t.prev = t.next) {
                  case 0:
                    if (!(this.word.length > 2)) {
                      t.next = 4;
                      break;
                    }

                    return this.searchParams.word = this.word, t.next = 4, this.$store.dispatch("SetsService/loadProducts", this.searchParams);

                  case 4:
                  case "end":
                    return t.stop();
                }
              }
            }, t, this);
          }));

          function e() {
            return t.apply(this, arguments);
          }

          return e;
        }()
      }]), r;
    }(l["c"]);

    H = Object(c["a"])([Object(l["a"])({
      components: {}
    })], H);
    var N,
        q = H,
        X = q,
        M = (r("4f0e"), Object(f["a"])(X, z, I, !1, null, "633f0149", null)),
        W = M.exports,
        J = r("2f62"),
        A = r("ade3"),
        D = (r("d3b7"), r("b0c0"), {
      getSets: function getSets() {
        return Object(x["a"])(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function t() {
          return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function (t) {
            while (1) {
              switch (t.prev = t.next) {
                case 0:
                  return t.abrupt("return", fetch(C("api/sets"), {
                    method: "GET",
                    credentials: "same-origin",
                    headers: new Headers({
                      "Content-Type": "application/json; charset=utf-8",
                      "X-Requested-Width": "XMLHttpRequest"
                    })
                  }).then(function (t) {
                    return t.json();
                  }));

                case 1:
                case "end":
                  return t.stop();
              }
            }
          }, t);
        }))();
      },
      completingSets: function completingSets(t) {
        return Object(x["a"])(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function e() {
          return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function (e) {
            while (1) {
              switch (e.prev = e.next) {
                case 0:
                  return e.abrupt("return", fetch(C("api/sets/" + t.setId + "/completing"), {
                    method: "POST",
                    credentials: "same-origin",
                    headers: new Headers({
                      "Content-Type": "application/json; charset=utf-8",
                      "X-Requested-Width": "XMLHttpRequest"
                    }),
                    body: JSON.stringify({
                      number: t.count
                    })
                  }).then(function (t) {
                    return t.json();
                  }));

                case 1:
                case "end":
                  return e.stop();
              }
            }
          }, e);
        }))();
      },
      disassemblySets: function disassemblySets(t) {
        return Object(x["a"])(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function e() {
          return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function (e) {
            while (1) {
              switch (e.prev = e.next) {
                case 0:
                  return e.abrupt("return", fetch(C("api/sets/" + t.setId + "/disassembly"), {
                    method: "POST",
                    credentials: "same-origin",
                    headers: new Headers({
                      "Content-Type": "application/json; charset=utf-8",
                      "X-Requested-Width": "XMLHttpRequest"
                    }),
                    body: JSON.stringify({
                      number: t.count
                    })
                  }).then(function (t) {
                    return t.json();
                  }));

                case 1:
                case "end":
                  return e.stop();
              }
            }
          }, e);
        }))();
      },
      deleteSet: function deleteSet(t) {
        return Object(x["a"])(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function e() {
          return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function (e) {
            while (1) {
              switch (e.prev = e.next) {
                case 0:
                  return e.abrupt("return", fetch(C("api/sets/" + t), {
                    method: "DELETE",
                    credentials: "same-origin",
                    headers: new Headers({
                      "Content-Type": "application/json; charset=utf-8",
                      "X-Requested-Width": "XMLHttpRequest"
                    }),
                    body: JSON.stringify({})
                  }).then(function (t) {
                    return t.json();
                  }));

                case 1:
                case "end":
                  return e.stop();
              }
            }
          }, e);
        }))();
      },
      products: function products(t) {
        return Object(x["a"])(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function e() {
          return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function (e) {
            while (1) {
              switch (e.prev = e.next) {
                case 0:
                  return e.abrupt("return", fetch(C("api/sets/products"), {
                    method: "POST",
                    credentials: "same-origin",
                    headers: new Headers({
                      "Content-Type": "application/json; charset=utf-8",
                      "X-Requested-Width": "XMLHttpRequest"
                    }),
                    body: JSON.stringify({
                      name: t.name ? t.name : "",
                      symbol: t.symbol ? t.symbol : "",
                      manufacturer: t.manufacturer ? t.manufacturer : "",
                      word: t.word ? t.word : ""
                    })
                  }).then(function (t) {
                    return t.json();
                  }));

                case 1:
                case "end":
                  return e.stop();
              }
            }
          }, e);
        }))();
      }
    }),
        Z = "SETS_SET_ALL",
        F = "SETS_SET_ERROR",
        G = "SETS_SET_IS_LOADING",
        U = "SETS_SET_PRODUCTS",
        V = !0,
        Y = {
      error: "",
      isLoading: !1,
      sets: [],
      products: []
    },
        B = {
      isLoading: function isLoading(t) {
        return t.isLoading;
      },
      error: function error(t) {
        return t.error;
      },
      sets: function sets(t) {
        return t.sets;
      },
      products: function products(t) {
        return t.products;
      }
    },
        K = {
      loadSets: function loadSets(t) {
        var e = t.commit;
        return e(G, !0), D.getSets().then(function (t) {
          return e(G, !1), e(Z, t), t;
        })["catch"](function (t) {
          e(F, t.message);
        });
      },
      completing: function completing(t, e) {
        var r = t.commit;
        return r(G, !0), D.completingSets(e).then(function (t) {
          return r(G, !1), t;
        })["catch"](function (t) {
          r(F, t.message);
        });
      },
      disassembly: function disassembly(t, e) {
        var r = t.commit;
        return r(G, !0), D.disassemblySets(e).then(function (t) {
          return r(G, !1), t;
        })["catch"](function (t) {
          r(F, t.message);
        });
      },
      "delete": function _delete(t, e) {
        var r = t.commit;
        return r(G, !0), D.deleteSet(e).then(function (t) {
          return r(G, !1), t;
        })["catch"](function (t) {
          r(F, t.message);
        });
      },
      loadProducts: function loadProducts(t, e) {
        var r = t.commit;
        return r(G, !0), D.products(e).then(function (t) {
          return r(G, !1), r(U, t), t;
        })["catch"](function (t) {
          r(F, t.message);
        });
      }
    },
        Q = (N = {}, Object(A["a"])(N, Z, function (t, e) {
      t.sets = e;
    }), Object(A["a"])(N, U, function (t, e) {
      t.products = e;
    }), Object(A["a"])(N, F, function (t, e) {
      t.error = e;
    }), Object(A["a"])(N, G, function (t, e) {
      t.isLoading = e;
    }), N),
        tt = {
      namespaced: V,
      state: Y,
      getters: B,
      actions: K,
      mutations: Q
    };
    n["a"].use(J["a"]);
    var et = new J["a"].Store({
      modules: {
        SetsService: tt
      }
    });
    n["a"].config.productionTip = !1, new n["a"]({
      store: et,
      render: function render(t) {
        return t(S);
      }
    }).$mount("#app"), new n["a"]({
      store: et,
      render: function render(t) {
        return t(L);
      }
    }).$mount("#setsList"), new n["a"]({
      store: et,
      render: function render(t) {
        return t(W);
      }
    }).$mount("#setEdit");
  },
  cf05: function cf05(t, e, r) {
    t.exports = r.p + "img/logo.82b9c7a5.png";
  }
});

/***/ }),

/***/ "./resources/layout/node_modules/@babel/runtime/regenerator/index.js":
/*!***************************************************************************!*\
  !*** ./resources/layout/node_modules/@babel/runtime/regenerator/index.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! regenerator-runtime */ "./resources/layout/node_modules/regenerator-runtime/runtime.js");


/***/ }),

/***/ "./resources/layout/node_modules/regenerator-runtime/runtime.js":
/*!**********************************************************************!*\
  !*** ./resources/layout/node_modules/regenerator-runtime/runtime.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

var runtime = (function (exports) {
  "use strict";

  var Op = Object.prototype;
  var hasOwn = Op.hasOwnProperty;
  var undefined; // More compressible than void 0.
  var $Symbol = typeof Symbol === "function" ? Symbol : {};
  var iteratorSymbol = $Symbol.iterator || "@@iterator";
  var asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator";
  var toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";

  function define(obj, key, value) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
    return obj[key];
  }
  try {
    // IE 8 has a broken Object.defineProperty that only works on DOM objects.
    define({}, "");
  } catch (err) {
    define = function(obj, key, value) {
      return obj[key] = value;
    };
  }

  function wrap(innerFn, outerFn, self, tryLocsList) {
    // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.
    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;
    var generator = Object.create(protoGenerator.prototype);
    var context = new Context(tryLocsList || []);

    // The ._invoke method unifies the implementations of the .next,
    // .throw, and .return methods.
    generator._invoke = makeInvokeMethod(innerFn, self, context);

    return generator;
  }
  exports.wrap = wrap;

  // Try/catch helper to minimize deoptimizations. Returns a completion
  // record like context.tryEntries[i].completion. This interface could
  // have been (and was previously) designed to take a closure to be
  // invoked without arguments, but in all the cases we care about we
  // already have an existing method we want to call, so there's no need
  // to create a new function object. We can even get away with assuming
  // the method takes exactly one argument, since that happens to be true
  // in every case, so we don't have to touch the arguments object. The
  // only additional allocation required is the completion record, which
  // has a stable shape and so hopefully should be cheap to allocate.
  function tryCatch(fn, obj, arg) {
    try {
      return { type: "normal", arg: fn.call(obj, arg) };
    } catch (err) {
      return { type: "throw", arg: err };
    }
  }

  var GenStateSuspendedStart = "suspendedStart";
  var GenStateSuspendedYield = "suspendedYield";
  var GenStateExecuting = "executing";
  var GenStateCompleted = "completed";

  // Returning this object from the innerFn has the same effect as
  // breaking out of the dispatch switch statement.
  var ContinueSentinel = {};

  // Dummy constructor functions that we use as the .constructor and
  // .constructor.prototype properties for functions that return Generator
  // objects. For full spec compliance, you may wish to configure your
  // minifier not to mangle the names of these two functions.
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}

  // This is a polyfill for %IteratorPrototype% for environments that
  // don't natively support it.
  var IteratorPrototype = {};
  IteratorPrototype[iteratorSymbol] = function () {
    return this;
  };

  var getProto = Object.getPrototypeOf;
  var NativeIteratorPrototype = getProto && getProto(getProto(values([])));
  if (NativeIteratorPrototype &&
      NativeIteratorPrototype !== Op &&
      hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) {
    // This environment has a native %IteratorPrototype%; use it instead
    // of the polyfill.
    IteratorPrototype = NativeIteratorPrototype;
  }

  var Gp = GeneratorFunctionPrototype.prototype =
    Generator.prototype = Object.create(IteratorPrototype);
  GeneratorFunction.prototype = Gp.constructor = GeneratorFunctionPrototype;
  GeneratorFunctionPrototype.constructor = GeneratorFunction;
  GeneratorFunction.displayName = define(
    GeneratorFunctionPrototype,
    toStringTagSymbol,
    "GeneratorFunction"
  );

  // Helper for defining the .next, .throw, and .return methods of the
  // Iterator interface in terms of a single ._invoke method.
  function defineIteratorMethods(prototype) {
    ["next", "throw", "return"].forEach(function(method) {
      define(prototype, method, function(arg) {
        return this._invoke(method, arg);
      });
    });
  }

  exports.isGeneratorFunction = function(genFun) {
    var ctor = typeof genFun === "function" && genFun.constructor;
    return ctor
      ? ctor === GeneratorFunction ||
        // For the native GeneratorFunction constructor, the best we can
        // do is to check its .name property.
        (ctor.displayName || ctor.name) === "GeneratorFunction"
      : false;
  };

  exports.mark = function(genFun) {
    if (Object.setPrototypeOf) {
      Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);
    } else {
      genFun.__proto__ = GeneratorFunctionPrototype;
      define(genFun, toStringTagSymbol, "GeneratorFunction");
    }
    genFun.prototype = Object.create(Gp);
    return genFun;
  };

  // Within the body of any async function, `await x` is transformed to
  // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test
  // `hasOwn.call(value, "__await")` to determine if the yielded value is
  // meant to be awaited.
  exports.awrap = function(arg) {
    return { __await: arg };
  };

  function AsyncIterator(generator, PromiseImpl) {
    function invoke(method, arg, resolve, reject) {
      var record = tryCatch(generator[method], generator, arg);
      if (record.type === "throw") {
        reject(record.arg);
      } else {
        var result = record.arg;
        var value = result.value;
        if (value &&
            typeof value === "object" &&
            hasOwn.call(value, "__await")) {
          return PromiseImpl.resolve(value.__await).then(function(value) {
            invoke("next", value, resolve, reject);
          }, function(err) {
            invoke("throw", err, resolve, reject);
          });
        }

        return PromiseImpl.resolve(value).then(function(unwrapped) {
          // When a yielded Promise is resolved, its final value becomes
          // the .value of the Promise<{value,done}> result for the
          // current iteration.
          result.value = unwrapped;
          resolve(result);
        }, function(error) {
          // If a rejected Promise was yielded, throw the rejection back
          // into the async generator function so it can be handled there.
          return invoke("throw", error, resolve, reject);
        });
      }
    }

    var previousPromise;

    function enqueue(method, arg) {
      function callInvokeWithMethodAndArg() {
        return new PromiseImpl(function(resolve, reject) {
          invoke(method, arg, resolve, reject);
        });
      }

      return previousPromise =
        // If enqueue has been called before, then we want to wait until
        // all previous Promises have been resolved before calling invoke,
        // so that results are always delivered in the correct order. If
        // enqueue has not been called before, then it is important to
        // call invoke immediately, without waiting on a callback to fire,
        // so that the async generator function has the opportunity to do
        // any necessary setup in a predictable way. This predictability
        // is why the Promise constructor synchronously invokes its
        // executor callback, and why async functions synchronously
        // execute code before the first await. Since we implement simple
        // async functions in terms of async generators, it is especially
        // important to get this right, even though it requires care.
        previousPromise ? previousPromise.then(
          callInvokeWithMethodAndArg,
          // Avoid propagating failures to Promises returned by later
          // invocations of the iterator.
          callInvokeWithMethodAndArg
        ) : callInvokeWithMethodAndArg();
    }

    // Define the unified helper method that is used to implement .next,
    // .throw, and .return (see defineIteratorMethods).
    this._invoke = enqueue;
  }

  defineIteratorMethods(AsyncIterator.prototype);
  AsyncIterator.prototype[asyncIteratorSymbol] = function () {
    return this;
  };
  exports.AsyncIterator = AsyncIterator;

  // Note that simple async functions are implemented on top of
  // AsyncIterator objects; they just return a Promise for the value of
  // the final result produced by the iterator.
  exports.async = function(innerFn, outerFn, self, tryLocsList, PromiseImpl) {
    if (PromiseImpl === void 0) PromiseImpl = Promise;

    var iter = new AsyncIterator(
      wrap(innerFn, outerFn, self, tryLocsList),
      PromiseImpl
    );

    return exports.isGeneratorFunction(outerFn)
      ? iter // If outerFn is a generator, return the full iterator.
      : iter.next().then(function(result) {
          return result.done ? result.value : iter.next();
        });
  };

  function makeInvokeMethod(innerFn, self, context) {
    var state = GenStateSuspendedStart;

    return function invoke(method, arg) {
      if (state === GenStateExecuting) {
        throw new Error("Generator is already running");
      }

      if (state === GenStateCompleted) {
        if (method === "throw") {
          throw arg;
        }

        // Be forgiving, per 25.3.3.3.3 of the spec:
        // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume
        return doneResult();
      }

      context.method = method;
      context.arg = arg;

      while (true) {
        var delegate = context.delegate;
        if (delegate) {
          var delegateResult = maybeInvokeDelegate(delegate, context);
          if (delegateResult) {
            if (delegateResult === ContinueSentinel) continue;
            return delegateResult;
          }
        }

        if (context.method === "next") {
          // Setting context._sent for legacy support of Babel's
          // function.sent implementation.
          context.sent = context._sent = context.arg;

        } else if (context.method === "throw") {
          if (state === GenStateSuspendedStart) {
            state = GenStateCompleted;
            throw context.arg;
          }

          context.dispatchException(context.arg);

        } else if (context.method === "return") {
          context.abrupt("return", context.arg);
        }

        state = GenStateExecuting;

        var record = tryCatch(innerFn, self, context);
        if (record.type === "normal") {
          // If an exception is thrown from innerFn, we leave state ===
          // GenStateExecuting and loop back for another invocation.
          state = context.done
            ? GenStateCompleted
            : GenStateSuspendedYield;

          if (record.arg === ContinueSentinel) {
            continue;
          }

          return {
            value: record.arg,
            done: context.done
          };

        } else if (record.type === "throw") {
          state = GenStateCompleted;
          // Dispatch the exception by looping back around to the
          // context.dispatchException(context.arg) call above.
          context.method = "throw";
          context.arg = record.arg;
        }
      }
    };
  }

  // Call delegate.iterator[context.method](context.arg) and handle the
  // result, either by returning a { value, done } result from the
  // delegate iterator, or by modifying context.method and context.arg,
  // setting context.delegate to null, and returning the ContinueSentinel.
  function maybeInvokeDelegate(delegate, context) {
    var method = delegate.iterator[context.method];
    if (method === undefined) {
      // A .throw or .return when the delegate iterator has no .throw
      // method always terminates the yield* loop.
      context.delegate = null;

      if (context.method === "throw") {
        // Note: ["return"] must be used for ES3 parsing compatibility.
        if (delegate.iterator["return"]) {
          // If the delegate iterator has a return method, give it a
          // chance to clean up.
          context.method = "return";
          context.arg = undefined;
          maybeInvokeDelegate(delegate, context);

          if (context.method === "throw") {
            // If maybeInvokeDelegate(context) changed context.method from
            // "return" to "throw", let that override the TypeError below.
            return ContinueSentinel;
          }
        }

        context.method = "throw";
        context.arg = new TypeError(
          "The iterator does not provide a 'throw' method");
      }

      return ContinueSentinel;
    }

    var record = tryCatch(method, delegate.iterator, context.arg);

    if (record.type === "throw") {
      context.method = "throw";
      context.arg = record.arg;
      context.delegate = null;
      return ContinueSentinel;
    }

    var info = record.arg;

    if (! info) {
      context.method = "throw";
      context.arg = new TypeError("iterator result is not an object");
      context.delegate = null;
      return ContinueSentinel;
    }

    if (info.done) {
      // Assign the result of the finished delegate to the temporary
      // variable specified by delegate.resultName (see delegateYield).
      context[delegate.resultName] = info.value;

      // Resume execution at the desired location (see delegateYield).
      context.next = delegate.nextLoc;

      // If context.method was "throw" but the delegate handled the
      // exception, let the outer generator proceed normally. If
      // context.method was "next", forget context.arg since it has been
      // "consumed" by the delegate iterator. If context.method was
      // "return", allow the original .return call to continue in the
      // outer generator.
      if (context.method !== "return") {
        context.method = "next";
        context.arg = undefined;
      }

    } else {
      // Re-yield the result returned by the delegate method.
      return info;
    }

    // The delegate iterator is finished, so forget it and continue with
    // the outer generator.
    context.delegate = null;
    return ContinueSentinel;
  }

  // Define Generator.prototype.{next,throw,return} in terms of the
  // unified ._invoke helper method.
  defineIteratorMethods(Gp);

  define(Gp, toStringTagSymbol, "Generator");

  // A Generator should always return itself as the iterator object when the
  // @@iterator function is called on it. Some browsers' implementations of the
  // iterator prototype chain incorrectly implement this, causing the Generator
  // object to not be returned from this call. This ensures that doesn't happen.
  // See https://github.com/facebook/regenerator/issues/274 for more details.
  Gp[iteratorSymbol] = function() {
    return this;
  };

  Gp.toString = function() {
    return "[object Generator]";
  };

  function pushTryEntry(locs) {
    var entry = { tryLoc: locs[0] };

    if (1 in locs) {
      entry.catchLoc = locs[1];
    }

    if (2 in locs) {
      entry.finallyLoc = locs[2];
      entry.afterLoc = locs[3];
    }

    this.tryEntries.push(entry);
  }

  function resetTryEntry(entry) {
    var record = entry.completion || {};
    record.type = "normal";
    delete record.arg;
    entry.completion = record;
  }

  function Context(tryLocsList) {
    // The root entry object (effectively a try statement without a catch
    // or a finally block) gives us a place to store values thrown from
    // locations where there is no enclosing try statement.
    this.tryEntries = [{ tryLoc: "root" }];
    tryLocsList.forEach(pushTryEntry, this);
    this.reset(true);
  }

  exports.keys = function(object) {
    var keys = [];
    for (var key in object) {
      keys.push(key);
    }
    keys.reverse();

    // Rather than returning an object with a next method, we keep
    // things simple and return the next function itself.
    return function next() {
      while (keys.length) {
        var key = keys.pop();
        if (key in object) {
          next.value = key;
          next.done = false;
          return next;
        }
      }

      // To avoid creating an additional object, we just hang the .value
      // and .done properties off the next function object itself. This
      // also ensures that the minifier will not anonymize the function.
      next.done = true;
      return next;
    };
  };

  function values(iterable) {
    if (iterable) {
      var iteratorMethod = iterable[iteratorSymbol];
      if (iteratorMethod) {
        return iteratorMethod.call(iterable);
      }

      if (typeof iterable.next === "function") {
        return iterable;
      }

      if (!isNaN(iterable.length)) {
        var i = -1, next = function next() {
          while (++i < iterable.length) {
            if (hasOwn.call(iterable, i)) {
              next.value = iterable[i];
              next.done = false;
              return next;
            }
          }

          next.value = undefined;
          next.done = true;

          return next;
        };

        return next.next = next;
      }
    }

    // Return an iterator with no values.
    return { next: doneResult };
  }
  exports.values = values;

  function doneResult() {
    return { value: undefined, done: true };
  }

  Context.prototype = {
    constructor: Context,

    reset: function(skipTempReset) {
      this.prev = 0;
      this.next = 0;
      // Resetting context._sent for legacy support of Babel's
      // function.sent implementation.
      this.sent = this._sent = undefined;
      this.done = false;
      this.delegate = null;

      this.method = "next";
      this.arg = undefined;

      this.tryEntries.forEach(resetTryEntry);

      if (!skipTempReset) {
        for (var name in this) {
          // Not sure about the optimal order of these conditions:
          if (name.charAt(0) === "t" &&
              hasOwn.call(this, name) &&
              !isNaN(+name.slice(1))) {
            this[name] = undefined;
          }
        }
      }
    },

    stop: function() {
      this.done = true;

      var rootEntry = this.tryEntries[0];
      var rootRecord = rootEntry.completion;
      if (rootRecord.type === "throw") {
        throw rootRecord.arg;
      }

      return this.rval;
    },

    dispatchException: function(exception) {
      if (this.done) {
        throw exception;
      }

      var context = this;
      function handle(loc, caught) {
        record.type = "throw";
        record.arg = exception;
        context.next = loc;

        if (caught) {
          // If the dispatched exception was caught by a catch block,
          // then let that catch block handle the exception normally.
          context.method = "next";
          context.arg = undefined;
        }

        return !! caught;
      }

      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        var record = entry.completion;

        if (entry.tryLoc === "root") {
          // Exception thrown outside of any try block that could handle
          // it, so set the completion value of the entire function to
          // throw the exception.
          return handle("end");
        }

        if (entry.tryLoc <= this.prev) {
          var hasCatch = hasOwn.call(entry, "catchLoc");
          var hasFinally = hasOwn.call(entry, "finallyLoc");

          if (hasCatch && hasFinally) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            } else if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else if (hasCatch) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            }

          } else if (hasFinally) {
            if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else {
            throw new Error("try statement without catch or finally");
          }
        }
      }
    },

    abrupt: function(type, arg) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc <= this.prev &&
            hasOwn.call(entry, "finallyLoc") &&
            this.prev < entry.finallyLoc) {
          var finallyEntry = entry;
          break;
        }
      }

      if (finallyEntry &&
          (type === "break" ||
           type === "continue") &&
          finallyEntry.tryLoc <= arg &&
          arg <= finallyEntry.finallyLoc) {
        // Ignore the finally entry if control is not jumping to a
        // location outside the try/catch block.
        finallyEntry = null;
      }

      var record = finallyEntry ? finallyEntry.completion : {};
      record.type = type;
      record.arg = arg;

      if (finallyEntry) {
        this.method = "next";
        this.next = finallyEntry.finallyLoc;
        return ContinueSentinel;
      }

      return this.complete(record);
    },

    complete: function(record, afterLoc) {
      if (record.type === "throw") {
        throw record.arg;
      }

      if (record.type === "break" ||
          record.type === "continue") {
        this.next = record.arg;
      } else if (record.type === "return") {
        this.rval = this.arg = record.arg;
        this.method = "return";
        this.next = "end";
      } else if (record.type === "normal" && afterLoc) {
        this.next = afterLoc;
      }

      return ContinueSentinel;
    },

    finish: function(finallyLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.finallyLoc === finallyLoc) {
          this.complete(entry.completion, entry.afterLoc);
          resetTryEntry(entry);
          return ContinueSentinel;
        }
      }
    },

    "catch": function(tryLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc === tryLoc) {
          var record = entry.completion;
          if (record.type === "throw") {
            var thrown = record.arg;
            resetTryEntry(entry);
          }
          return thrown;
        }
      }

      // The context.catch method must only be called with a location
      // argument that corresponds to a known catch block.
      throw new Error("illegal catch attempt");
    },

    delegateYield: function(iterable, resultName, nextLoc) {
      this.delegate = {
        iterator: values(iterable),
        resultName: resultName,
        nextLoc: nextLoc
      };

      if (this.method === "next") {
        // Deliberately forget the last sent value so that we don't
        // accidentally pass it on to the delegate.
        this.arg = undefined;
      }

      return ContinueSentinel;
    }
  };

  // Regardless of whether this script is executing as a CommonJS module
  // or not, return the runtime object so that we can declare the variable
  // regeneratorRuntime in the outer scope, which allows this module to be
  // injected easily by `bin/regenerator --include-runtime script.js`.
  return exports;

}(
  // If this script is executing as a CommonJS module, use module.exports
  // as the regeneratorRuntime namespace. Otherwise create a new empty
  // object. Either way, the resulting object will be used to initialize
  // the regeneratorRuntime variable at the top of this file.
   true ? module.exports : undefined
));

try {
  regeneratorRuntime = runtime;
} catch (accidentalStrictMode) {
  // This module should not be running in strict mode, so the above
  // assignment should always work unless something is misconfigured. Just
  // in case runtime.js accidentally runs in strict mode, we can escape
  // strict mode using a global Function call. This could conceivably fail
  // if a Content Security Policy forbids using Function, but in that case
  // the proper solution is to fix the accidental strict mode problem. If
  // you've misconfigured your bundler to force strict mode and applied a
  // CSP to forbid Function, and you're not willing to fix either of those
  // problems, please detail your unique predicament in a GitHub issue.
  Function("r", "regeneratorRuntime = r")(runtime);
}


/***/ }),

/***/ 1:
/*!********************************************************!*\
  !*** multi ./resources/layout/dist/js/app.f0c95102.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/adrian/admin-panel/resources/layout/dist/js/app.f0c95102.js */"./resources/layout/dist/js/app.f0c95102.js");


/***/ })

/******/ });