(function ($) {
	"use strict";

	function wbSetCookie(name, value) {
		document.cookie = name + "=" + value + "; path=/";
	}

	function wbGetCookie(name) {
		var cookieArray = document.cookie.split(";");

		for (var i = 0; i < cookieArray.length; i++) {
			var cookie = cookieArray[i].trim();

			if (cookie.indexOf(name + "=") === 0) {
				return cookie.substring(name.length + 1);
			}
		}

		return null;
	}

	$(document).ready(function () {
		$("#pa_location").closest("tr").hide();
	});

	$(document).on("change", "#wb-choose-location", function (e) {
		e.preventDefault();

		var val = $(this).val();

		wbSetCookie("wb_loc", val);
	});

	$(document).on("click", ".wb-check-location-store-open-btn", function (e) {
		e.preventDefault();

		$(".wb-check-location-store-popup").show();

		const attr = new Object();

		$("body")
			.find(".wb_product_attr")
			.each(function (i, e) {
				const name = $(e).data("attr");
				attr[name] = $(e).val();
			});

		$.ajax({
			url: wb_loc_vars.ajax_url,
			type: "get",
			data: {
				action: "wb_get_location_store",
				attr,
				nonce: wb_loc_vars.ajax_nonce.get_location_store,
				product_id: $("input[name='product_id']").val(),
			},
			beforeSend: function () {
				$(".wb-popup-content-loading").html(
					"<p>Sedang mengambil data toko ...</p>"
				);
				$(".wb-popup-content-loading").show();
				$(".wb-location-stores").hide();
				$(".wb-location-stores").html("");
			},
			success: function (response) {
				$(".wb-popup-content-loading").hide();
				$(".wb-location-stores").show();
				var loc_stores_html = response.loc_stores_html;

				if (loc_stores_html) {
					$(".wb-location-stores").html(loc_stores_html);
				} else {
					$(".wb-location-stores").hide();
					$(".wb-popup-content-loading").show();
					$(".wb-popup-content-loading").html(
						"<p>Tidak ada toko ditemukan untuk size tersebut, silahkan pilih size lainnya</p>"
					);
				}
			},
		});
	});

	$(document).on("click", ".wb-check-location-store-close-btn", function (e) {
		e.preventDefault();

		$(".wb-check-location-store-popup").hide();
	});

	$(document).on("click", ".reset_variations", function (e) {
		e.preventDefault();

		var loc = wb_loc_vars.loc_default;
		var wb_loc = wbGetCookie("wb_loc");
		if (wb_loc) {
			loc = wb_loc;
		}
		$("#pa_location").val(loc);
		$("#pa_location").trigger("change");
	});

	// $(document).on("change", "#pa_location", function (e) {
	// 	e.preventDefault();

	// 	var loc_name = $("#pa_location option:selected").text();
	// 	$(".wb-loc-store-name").html(loc_name);

	// 	if ($(".stock.out-of-stock").length > 0) {
	// 		$(".wb-check-location-store-open-btn").css("display", "inline-block");
	// 	} else {
	// 		$(".wb-check-location-store-open-btn").hide();
	// 	}
	// });

	$(document).on("wvs-selected-item", function (value, select, element) {
		console.log({
			value: value,
			select: select,
			element: element,
		});

		$(document)
			.find(".woocommerce-variation-add-to-cart")
			.addClass("variation-selected");

		var attr_name = $(value.target)
			.parents(".variable-items-wrapper")
			.data("attribute_name")
			.replace("attribute_", "");

		var attr_val = $(value.target).data("value");

		$("#wb_product_attr_" + attr_name).val(attr_val);
	});

	$(document).on("click", ".wb-location-store-choose-btn", function (e) {
		return;
		e.preventDefault();

		const attr = $(this).data("attribute");
		const location = $(this).data("location");
		const loc_slug = $(this).data("slug-location");

		$.each(attr, function (key, value) {
			const select = $("[name=" + key + "]");

			//check if select has the option, if not then add it
			if (select.find("option[value='" + value + "']").length == 0) {
				console.log("adding option");
				select.append(new Option(value, value)).val(value).trigger("change");
			} else {
				select.val(value).trigger("change");
			}
		});

		$(".wb-loc-store-name").html(location);
		wbSetCookie("wb_loc", loc_slug);
		$(".wb_product_attr_pa_location").val(loc_slug);
		$(".wb-check-location-store-close-btn").trigger("click");
	});
})(jQuery);
