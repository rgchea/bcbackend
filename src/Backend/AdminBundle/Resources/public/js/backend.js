
	function changeSubCategory(myUrl, dataValue, loadItemsObjectID, subcategory){
		
		if(dataValue != 0){
			
			$.ajax({
				type:"POST",
				dataType:"json",
				url: myUrl,
				data:"category_id="+dataValue,
				success:function(data){
					
					if (!isEmpty(data)){
						
						var items = [];
						
						  $.each( data, function( key, val ) {
						    items.push( "<option value='" + key + "'>" + val + "</li>" );
						  });
											
						$("#"+loadItemsObjectID).html(items);
						if(subcategory != 0){
							
							if($("#"+loadItemsObjectID).find('option[value="'+subcategory +'"]').length > 0){
								$("#"+loadItemsObjectID).val(subcategory);
							}		
							
						}	
						
					}
					else{
						$("#"+loadItemsObjectID).html("<option value='0'>---</option>");
					}
					
					
					
				}
			});	 
			
		}else{
            $("#"+loadItemsObjectID).html("<option value='0'>---</option>");
        }
	}
	
	function isEmpty(obj) {
	    return Object.keys(obj).length === 0;
	}	
