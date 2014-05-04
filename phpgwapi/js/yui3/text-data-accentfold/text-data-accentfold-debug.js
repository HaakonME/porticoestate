/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add('text-data-accentfold', function (Y, NAME) {

// The following tool was very helpful in creating these mappings:
// http://unicode.org/cldr/utility/list-unicodeset.jsp?a=[:toNFKD%3D/^a/:]&abb=on

Y.namespace('Text.Data').AccentFold = {
    0: /[\u2070\u2080\u24EA\uFF10]/gi,
    1: /[\u00B9\u2081\u2460\uFF11]/gi,
    2: /[\u00B2\u2082\u2461\uFF12]/gi,
    3: /[\u00B3\u2083\u2462\uFF13]/gi,
    4: /[\u2074\u2084\u2463\uFF14]/gi,
    5: /[\u2075\u2085\u2464\uFF15]/gi,
    6: /[\u2076\u2086\u2465\uFF16]/gi,
    7: /[\u2077\u2087\u2466\uFF17]/gi,
    8: /[\u2078\u2088\u2467\uFF18]/gi,
    9: /[\u2079\u2089\u2468\uFF19]/gi,
    a: /[\u00AA\u00E0-\u00E5\u0101\u0103\u0105\u01CE\u01DF\u01E1\u01FB\u0201\u0203\u0227\u1D43\u1E01\u1E9A\u1EA1\u1EA3\u1EA5\u1EA7\u1EA9\u1EAB\u1EAD\u1EAF\u1EB1\u1EB3\u1EB5\u1EB7\u24D0\uFF41]/gi,
    b: /[\u1D47\u1E03\u1E05\u1E07\u24D1\uFF42]/gi,
    c: /[\u00E7\u0107\u0109\u010B\u010D\u1D9C\u1E09\u24D2\uFF43]/gi,
    d: /[\u010F\u1D48\u1E0B\u1E0D\u1E0F\u1E11\u1E13\u217E\u24D3\uFF44]/gi,
    e: /[\u00E8-\u00EB\u0113\u0115\u0117\u0119\u011B\u0205\u0207\u0229\u1D49\u1E15\u1E17\u1E19\u1E1B\u1E1D\u1EB9\u1EBB\u1EBD\u1EBF\u1EC1\u1EC3\u1EC5\u1EC7\u2091\u212F\u24D4\uFF45]/gi,
    f: /[\u1DA0\u1E1F\u24D5\uFF46]/gi,
    g: /[\u011D\u011F\u0121\u0123\u01E7\u01F5\u1D4D\u1E21\u210A\u24D6\uFF47]/gi,
    h: /[\u0125\u021F\u02B0\u1E23\u1E25\u1E27\u1E29\u1E2B\u1E96\u210E\u24D7\uFF48]/gi,
    i: /[\u00EC-\u00EF\u0129\u012B\u012D\u012F\u0133\u01D0\u0209\u020B\u1D62\u1E2D\u1E2F\u1EC9\u1ECB\u2071\u2139\u2170\u24D8\uFF49]/gi,
    j: /[\u0135\u01F0\u02B2\u24D9\u2C7C\uFF4A]/gi,
    k: /[\u0137\u01E9\u1D4F\u1E31\u1E33\u1E35\u24DA\uFF4B]/gi,
    l: /[\u013A\u013C\u013E\u0140\u01C9\u02E1\u1E37\u1E39\u1E3B\u1E3D\u2113\u217C\u24DB\uFF4C]/gi,
    m: /[\u1D50\u1E3F\u1E41\u1E43\u217F\u24DC\uFF4D]/gi,
    n: /[\u00F1\u0144\u0146\u0148\u01F9\u1E45\u1E47\u1E49\u1E4B\u207F\u24DD\uFF4E]/gi,
    o: /[\u00BA\u00F2-\u00F6\u014D\u014F\u0151\u01A1\u01D2\u01EB\u01ED\u020D\u020F\u022B\u022D\u022F\u0231\u1D52\u1E4D\u1E4F\u1E51\u1E53\u1ECD\u1ECF\u1ED1\u1ED3\u1ED5\u1ED7\u1ED9\u1EDB\u1EDD\u1EDF\u1EE1\u1EE3\u2092\u2134\u24DE\uFF4F]/gi,
    p: /[\u1D56\u1E55\u1E57\u24DF\uFF50]/gi,
    q: /[\u02A0\u24E0\uFF51]/gi,
    r: /[\u0155\u0157\u0159\u0211\u0213\u02B3\u1D63\u1E59\u1E5B\u1E5D\u1E5F\u24E1\uFF52]/gi,
    s: /[\u015B\u015D\u015F\u0161\u017F\u0219\u02E2\u1E61\u1E63\u1E65\u1E67\u1E69\u1E9B\u24E2\uFF53]/gi,
    t: /[\u0163\u0165\u021B\u1D57\u1E6B\u1E6D\u1E6F\u1E71\u1E97\u24E3\uFF54]/gi,
    u: /[\u00F9-\u00FC\u0169\u016B\u016D\u016F\u0171\u0173\u01B0\u01D4\u01D6\u01D8\u01DA\u01DC\u0215\u0217\u1D58\u1D64\u1E73\u1E75\u1E77\u1E79\u1E7B\u1EE5\u1EE7\u1EE9\u1EEB\u1EED\u1EEF\u1EF1\u24E4\uFF55]/gi,
    v: /[\u1D5B\u1D65\u1E7D\u1E7F\u2174\u24E5\uFF56]/gi,
    w: /[\u0175\u02B7\u1E81\u1E83\u1E85\u1E87\u1E89\u1E98\u24E6\uFF57]/gi,
    x: /[\u02E3\u1E8B\u1E8D\u2093\u2179\u24E7\uFF58]/gi,
    y: /[\u00FD\u00FF\u0177\u0233\u02B8\u1E8F\u1E99\u1EF3\u1EF5\u1EF7\u1EF9\u24E8\uFF59]/gi,
    z: /[\u017A\u017C\u017E\u1DBB\u1E91\u1E93\u1E95\u24E9\uFF5A]/gi
};


}, '3.16.0', {"requires": ["yui-base"]});
