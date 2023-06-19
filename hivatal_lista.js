	
	function set_date_of_xml() {
		var veletlen = Math.random;
		$.get("get_date_of_xml.php",
			{ sid: veletlen },
			function(valasz) { $("#date_of_xml").html(valasz); }
		);
	}

	function search() {
		var query = $("#query").val();
		var veletlen = Math.random;
		if (query.length<3) {
			$("#warn").html("A keresés indításához hez legalább 3 karaktert meg kell adni...");
			return;
		} else {
			$("#warn").html("");
		}
		$.get("hivatal_lista.php",
			{ query: query, sid: veletlen },
			function(valasz) { $("#capt").html(valasz); }
		);
		set_date_of_xml();
	}

	$(document).ready( function() {
		$("#query").keyup(search);
		$.get("get_hivatal_lista.php");
		set_date_of_xml();
	});
