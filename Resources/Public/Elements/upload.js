document.addEventListener("DOMContentLoaded", (event) => {
	clearUploadFieldsButtons = document.querySelectorAll('.ameos-form-elementupload-deletebutton');
	if(clearUploadFieldsButtons.length > 0){
		[].slice.call(clearUploadFieldsButtons).forEach(function(elem){
			elem.addEventListener('click',function(e){
                target = elem.id.replace('-deletebutton','');
                targetViewLink = document.getElementById(target+'-viewlink');
                targetInput = document.getElementById(target);
                targetViewLink.remove();
                targetInput.remove();
                elem.remove();
				targetUploadInput = document.getElementById(target.split('-')[0]+'-upload');
				targetUploadInput.focus();
			});
		});
	}

	resetUploadFieldsButtons = document.querySelectorAll('.ameos-form-elementupload-resetbutton');
	if(resetUploadFieldsButtons.length > 0){
		[].slice.call(resetUploadFieldsButtons).forEach(function(elem){
			elem.addEventListener('click',function(e){
				target = elem.id.replace('-uploadreset','');
				targetUploadInput = document.getElementById(target+'-upload');
				if(targetUploadInput.value){
					targetUploadInput.value = '';
				}
				targetUploadInput.focus();
			});
		});
	}
});