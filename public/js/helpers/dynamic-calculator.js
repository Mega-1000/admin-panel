$(document).on('change', '.price', function (event) {
        var parent = $(event.target).closest('form')
        var net_purchase_price_commercial_unit = parent.find('.net_purchase_price_commercial_unit');
        var net_purchase_price_basic_unit = parent.find('.net_purchase_price_basic_unit');
        var net_purchase_price_calculated_unit = parent.find('.net_purchase_price_calculated_unit');
        var net_purchase_price_aggregate_unit = parent.find('.net_purchase_price_aggregate_unit');

        var gross_purchase_price_commercial_unit = parent.find('.gross_purchase_price_commercial_unit');
        var gross_purchase_price_basic_unit = parent.find('.gross_purchase_price_basic_unit');
        var gross_purchase_price_calculated_unit = parent.find('.gross_purchase_price_calculated_unit');
        var gross_purchase_price_aggregate_unit = parent.find('.gross_purchase_price_aggregate_unit');

        var numbers_of_basic_commercial_units_in_pack = parent.find('.numbers_of_basic_commercial_units_in_pack');
        var number_of_sale_units_in_the_pack = parent.find('.number_of_sale_units_in_the_pack');
        var unit_consumption = parent.find('.unit_consumption');

        var net_purchase_price_commercial_unit_value = 0;
        if (!$(this).hasClass('gross_purchase_price_commercial_unit')) {
            net_purchase_price_commercial_unit_value = parseFloat(net_purchase_price_commercial_unit.val());
        } else {
            net_purchase_price_commercial_unit_value = parseFloat(gross_purchase_price_commercial_unit.val() / 1.23);
        }
        var net_purchase_price_basic_unit_value = 0;
        if (!$(this).hasClass('gross_purchase_price_basic_unit')) {
            net_purchase_price_basic_unit_value = parseFloat(net_purchase_price_basic_unit.val());
        } else {
            net_purchase_price_basic_unit_value = parseFloat(gross_purchase_price_basic_unit.val() / 1.23);
        }
        var net_purchase_price_aggregate_unit_value = 0;
        if (!$(this).hasClass('gross_purchase_price_aggregate_unit')) {
            net_purchase_price_aggregate_unit_value = parseFloat(net_purchase_price_aggregate_unit.val());
        } else {
            net_purchase_price_aggregate_unit_value = parseFloat(gross_purchase_price_aggregate_unit.val() / 1.23);
        }
        var net_purchase_price_calculated_unit_value = 0;
        if (!$(this).hasClass('gross_purchase_price_calculated_unit')) {
            net_purchase_price_calculated_unit_value = parseFloat(net_purchase_price_calculated_unit.val());
        } else {
            net_purchase_price_calculated_unit_value = parseFloat(gross_purchase_price_calculated_unit.val() / 1.23);
        }

        var numbers_of_basic_commercial_units_in_pack_value = parseFloat(numbers_of_basic_commercial_units_in_pack.val());
        var number_of_sale_units_in_the_pack_value = parseFloat(number_of_sale_units_in_the_pack.val());
        var unit_consumption_value = parseFloat(unit_consumption.val());
        var values = {
            'net_purchase_price_commercial_unit':
                {
                    'net_purchase_price_aggregate_unit': (net_purchase_price_commercial_unit_value * number_of_sale_units_in_the_pack_value).toFixed(4),
                    'net_purchase_price_basic_unit': (net_purchase_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value).toFixed(4),
                    'net_purchase_price_calculated_unit': ((net_purchase_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * unit_consumption_value).toFixed(4),
                    'gross_purchase_price_aggregate_unit': ((net_purchase_price_commercial_unit_value * number_of_sale_units_in_the_pack_value) * 1.23).toFixed(4),
                    'gross_purchase_price_basic_unit': ((net_purchase_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * 1.23).toFixed(4),
                    'gross_purchase_price_calculated_unit': (((net_purchase_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * unit_consumption_value) * 1.23).toFixed(4),
                },
            'net_purchase_price_basic_unit':
                {
                    'net_purchase_price_aggregate_unit': (net_purchase_price_basic_unit_value * number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value).toFixed(4),
                    'net_purchase_price_commercial_unit': (net_purchase_price_basic_unit_value * numbers_of_basic_commercial_units_in_pack_value).toFixed(2),
                    'net_purchase_price_calculated_unit': ((net_purchase_price_basic_unit_value * unit_consumption_value)).toFixed(4),
                    'gross_purchase_price_aggregate_unit': ((net_purchase_price_basic_unit_value * number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value * 1.23)).toFixed(4),
                    'gross_purchase_price_commercial_unit': ((net_purchase_price_basic_unit_value * numbers_of_basic_commercial_units_in_pack_value) * 1.23).toFixed(2),
                    'gross_purchase_price_calculated_unit': (((net_purchase_price_basic_unit_value * unit_consumption_value) * 1.23)).toFixed(4)
                },
            'net_purchase_price_aggregate_unit':
                {
                    'net_purchase_price_basic_unit': (net_purchase_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value)).toFixed(4),
                    'net_purchase_price_commercial_unit': (net_purchase_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value)).toFixed(2),
                    'net_purchase_price_calculated_unit': (net_purchase_price_aggregate_unit_value * (number_of_sale_units_in_the_pack_value)).toFixed(4),
                    'gross_purchase_price_basic_unit': ((net_purchase_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value)) * 1.23).toFixed(4),
                    'gross_purchase_price_commercial_unit': ((net_purchase_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(2),
                    'gross_purchase_price_calculated_unit': ((net_purchase_price_aggregate_unit_value * (number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(4),
                },
            'net_purchase_price_calculated_unit':
                {
                    'net_purchase_price_basic_unit': ((net_purchase_price_calculated_unit_value / unit_consumption_value)).toFixed(4),
                    'net_purchase_price_commercial_unit': ((net_purchase_price_calculated_unit_value / unit_consumption_value) * numbers_of_basic_commercial_units_in_pack_value).toFixed(2),
                    'net_purchase_price_aggregate_unit': (((net_purchase_price_calculated_unit_value * (number_of_sale_units_in_the_pack_value / unit_consumption_value)))).toFixed(4),
                    'gross_purchase_price_basic_unit': (((net_purchase_price_calculated_unit_value / unit_consumption_value)) * 1.23).toFixed(4),
                    'gross_purchase_price_commercial_unit': (((net_purchase_price_calculated_unit_value / unit_consumption_value) * numbers_of_basic_commercial_units_in_pack_value) * 1.23).toFixed(2),
                    'gross_purchase_price_aggregate_unit': (((net_purchase_price_calculated_unit_value * (number_of_sale_units_in_the_pack_value / unit_consumption_value))) * 1.23).toFixed(4),

                }
        }


        if ($(this).hasClass('net_purchase_price_commercial_unit')) {
            net_purchase_price_aggregate_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_aggregate_unit']);
            net_purchase_price_basic_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_basic_unit']);
            net_purchase_price_calculated_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_calculated_unit']);
            gross_purchase_price_commercial_unit.val(($(this).val() * 1.23).toFixed(2));
            gross_purchase_price_aggregate_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_aggregate_unit']);
            gross_purchase_price_basic_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_basic_unit']);
            gross_purchase_price_calculated_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_calculated_unit']);
        }

        if ($(this).hasClass('net_purchase_price_basic_unit')) {
            net_purchase_price_aggregate_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_aggregate_unit']);
            net_purchase_price_commercial_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_commercial_unit']);
            net_purchase_price_calculated_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_calculated_unit']);
            gross_purchase_price_basic_unit.val(($(this).val() * 1.23).toFixed(4));
            gross_purchase_price_aggregate_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_aggregate_unit']);
            gross_purchase_price_commercial_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_commercial_unit']);
            gross_purchase_price_calculated_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_calculated_unit']);
            $('.net_purchase_price_commercial_unit').change()
        }

        if ($(this).hasClass('net_purchase_price_aggregate_unit')) {
            net_purchase_price_basic_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_basic_unit']);
            net_purchase_price_commercial_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_commercial_unit']);
            net_purchase_price_calculated_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_calculated_unit']);
            gross_purchase_price_aggregate_unit.val(($(this).val() * 1.23).toFixed(4));
            gross_purchase_price_basic_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_basic_unit']);
            gross_purchase_price_commercial_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_commercial_unit']);
            gross_purchase_price_calculated_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_calculated_unit']);
            parent.find('.net_purchase_price_commercial_unit').change()
        }

        if ($(this).hasClass('net_purchase_price_calculated_unit')) {
            net_purchase_price_basic_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_basic_unit']);
            net_purchase_price_commercial_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_commercial_unit']);
            net_purchase_price_aggregate_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_aggregate_unit']);
            gross_purchase_price_calculated_unit.val(($(this).val() * 1.23).toFixed(4));
            gross_purchase_price_basic_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_basic_unit']);
            gross_purchase_price_commercial_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_commercial_unit']);
            gross_purchase_price_aggregate_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_aggregate_unit']);
            parent.find('.net_purchase_price_commercial_unit').change()
        }

        if ($(this).hasClass('gross_purchase_price_commercial_unit')) {
            net_purchase_price_aggregate_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_aggregate_unit']);
            net_purchase_price_basic_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_basic_unit']);
            net_purchase_price_calculated_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_calculated_unit']);
            net_purchase_price_commercial_unit.val(($(this).val() / 1.23).toFixed(2));
            gross_purchase_price_aggregate_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_aggregate_unit']);
            gross_purchase_price_basic_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_basic_unit']);
            gross_purchase_price_calculated_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_calculated_unit']);
        }

        if ($(this).hasClass('gross_purchase_price_basic_unit')) {
            net_purchase_price_aggregate_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_aggregate_unit']);
            net_purchase_price_commercial_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_commercial_unit']);
            net_purchase_price_calculated_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_calculated_unit']);
            net_purchase_price_basic_unit.val(($(this).val() / 1.23).toFixed(4));
            gross_purchase_price_aggregate_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_aggregate_unit']);
            gross_purchase_price_commercial_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_commercial_unit']);
            gross_purchase_price_calculated_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_calculated_unit']);
            parent.find('.gross_purchase_price_commercial_unit').change()
        }

        if ($(this).hasClass('gross_purchase_price_aggregate_unit')) {
            net_purchase_price_basic_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_basic_unit']);
            net_purchase_price_commercial_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_commercial_unit']);
            net_purchase_price_calculated_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_calculated_unit']);
            net_purchase_price_aggregate_unit.val(($(this).val() / 1.23).toFixed(4));
            gross_purchase_price_basic_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_basic_unit']);
            gross_purchase_price_commercial_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_commercial_unit']);
            gross_purchase_price_calculated_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_calculated_unit']);
            parent.find('.gross_purchase_price_commercial_unit').change()
        }

        if ($(this).hasClass('gross_purchase_price_calculated_unit')) {
            net_purchase_price_basic_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_basic_unit']);
            net_purchase_price_commercial_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_commercial_unit']);
            net_purchase_price_aggregate_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_aggregate_unit']);
            net_purchase_price_calculated_unit.val(($(this).val() / 1.23).toFixed(4));
            gross_purchase_price_basic_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_basic_unit']);
            gross_purchase_price_commercial_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_commercial_unit']);
            gross_purchase_price_aggregate_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_aggregate_unit']);
            parent.find('.gross_purchase_price_commercial_unit').change()
        }

        if ($(this).hasClass('net_selling_price_commercial_unit')) {
            net_selling_price_aggregate_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_aggregate_unit']);
            net_selling_price_basic_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_basic_unit']);
            net_selling_price_calculated_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_calculated_unit']);
            gross_selling_price_commercial_unit.val(($(this).val() * 1.23).toFixed(2));
            gross_selling_price_aggregate_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_aggregate_unit']);
            gross_selling_price_basic_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_basic_unit']);
            gross_selling_price_calculated_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_calculated_unit']);
        }

        if ($(this).hasClass('net_selling_price_basic_unit')) {
            net_selling_price_aggregate_unit.val(values['net_selling_price_basic_unit']['net_selling_price_aggregate_unit']);
            net_selling_price_commercial_unit.val(values['net_selling_price_basic_unit']['net_selling_price_commercial_unit']);
            net_selling_price_calculated_unit.val(values['net_selling_price_basic_unit']['net_selling_price_calculated_unit']);
            gross_selling_price_basic_unit.val(($(this).val() * 1.23).toFixed(4));
            gross_selling_price_aggregate_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_aggregate_unit']);
            gross_selling_price_commercial_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_commercial_unit']);
            gross_selling_price_calculated_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_calculated_unit']);
            parent.find('.net_selling_price_commercial_unit').change()
        }

        if ($(this).hasClass('net_selling_price_aggregate_unit')) {
            net_selling_price_basic_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_basic_unit']);
            net_selling_price_commercial_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_commercial_unit']);
            net_selling_price_calculated_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_calculated_unit']);
            gross_selling_price_aggregate_unit.val(($(this).val() * 1.23).toFixed(4));
            gross_selling_price_basic_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_basic_unit']);
            gross_selling_price_commercial_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_commercial_unit']);
            gross_selling_price_calculated_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_calculated_unit']);
            parent.find('.net_selling_price_commercial_unit').change()
        }

        if ($(this).hasClass('net_selling_price_calculated_unit')) {
            net_selling_price_basic_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_basic_unit']);
            net_selling_price_commercial_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_commercial_unit']);
            net_selling_price_aggregate_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_aggregate_unit']);
            gross_selling_price_calculated_unit.val(($(this).val() * 1.23).toFixed(4));
            gross_selling_price_basic_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_basic_unit']);
            gross_selling_price_commercial_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_commercial_unit']);
            gross_selling_price_aggregate_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_aggregate_unit']);
            parent.find('.net_selling_price_commercial_unit').change()
        }

        if ($(this).hasClass('gross_selling_price_commercial_unit')) {
            net_selling_price_aggregate_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_aggregate_unit']);
            net_selling_price_basic_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_basic_unit']);
            net_selling_price_calculated_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_calculated_unit']);
            net_selling_price_commercial_unit.val(($(this).val() / 1.23).toFixed(2));
            gross_selling_price_aggregate_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_aggregate_unit']);
            gross_selling_price_basic_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_basic_unit']);
            gross_selling_price_calculated_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_calculated_unit']);
        }

        if ($(this).hasClass('gross_selling_price_basic_unit')) {
            net_selling_price_aggregate_unit.val(values['net_selling_price_basic_unit']['net_selling_price_aggregate_unit']);
            net_selling_price_commercial_unit.val(values['net_selling_price_basic_unit']['net_selling_price_commercial_unit']);
            net_selling_price_calculated_unit.val(values['net_selling_price_basic_unit']['net_selling_price_calculated_unit']);
            net_selling_price_basic_unit.val(($(this).val() / 1.23).toFixed(4));
            gross_selling_price_aggregate_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_aggregate_unit']);
            gross_selling_price_commercial_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_commercial_unit']);
            gross_selling_price_calculated_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_calculated_unit']);
            parent.find('.gross_selling_price_commercial_unit').change()
        }

        if ($(this).hasClass('gross_selling_price_aggregate_unit')) {
            net_selling_price_basic_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_basic_unit']);
            net_selling_price_commercial_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_commercial_unit']);
            net_selling_price_calculated_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_calculated_unit']);
            net_selling_price_aggregate_unit.val(($(this).val() / 1.23).toFixed(4));
            gross_selling_price_basic_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_basic_unit']);
            gross_selling_price_commercial_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_commercial_unit']);
            gross_selling_price_calculated_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_calculated_unit']);
            parent.find('.gross_selling_price_commercial_unit').change()
        }

        if ($(this).hasClass('gross_selling_price_calculated_unit')) {
            net_selling_price_basic_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_basic_unit']);
            net_selling_price_commercial_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_commercial_unit']);
            net_selling_price_aggregate_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_aggregate_unit']);
            net_selling_price_calculated_unit.val(($(this).val() / 1.23).toFixed(4));
            gross_selling_price_basic_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_basic_unit']);
            gross_selling_price_commercial_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_commercial_unit']);
            gross_selling_price_aggregate_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_aggregate_unit']);
            parent.find('.gross_selling_price_commercial_unit').change()
        }
    }
);
