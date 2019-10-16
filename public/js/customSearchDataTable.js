$.fn.dataTable.ext.search.push(
    function(settings, data, dataIndex) {
        if (settings.oPreviousSearch.sSearch === "") return true; // Always return true if search is blank (save processing)

        var search = $.fn.DataTable.util.escapeRegex(settings.oPreviousSearch.sSearch);
        var newFilter = data.slice();

        for (var i = 0; i < settings.aoColumns.length; i++) {
            if (!settings.aoColumns[i].bVisible) {
                newFilter.splice(i, 1);
            }
        }

        var regex = new RegExp("^(?=.*?" + search + ").*$", "i");
        return regex.test(newFilter.join(" "));
    }
);