

	function changeProperties(myUrl, loadItemsObjectID, sectorID){

		$("#pizote_spinner").show();

		$.ajax({
			type:"POST",
			dataType:"json",
			url: myUrl,
			data:"sector_id="+sectorID,
			success:function(data){

				console.log(data);



                var items = [];

                $.each( data, function( key, val ) {
                    items.push( "<option value='" + val.id + "'>" + val.property_number + "</li>" );
                });

				$("#"+loadItemsObjectID).html(items);
				$("#pizote_spinner").hide();

			}
		});

	}


	
	function isEmpty(obj) {
	    return obj.length === 0;
	}	
