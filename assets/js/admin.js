jQuery(document).ready(function() {
    jQuery("[data-toggle=offcanvas]").click(function() {
      jQuery(".row-offcanvas").toggleClass("active");
    });
  });




   // sandbox disable popups
   if (window.self !== window.top && window.name != "view1") {
    window.alert = function() {
      /*disable alert*/
    };
    window.confirm = function() {
      /*disable confirm*/
    };
    window.prompt = function() {
      /*disable prompt*/
    };
    window.open = function() {
      /*disable open*/
    };
  }
  
  // prevent href=# click jump
  document.addEventListener(
    "DOMContentLoaded",
    function() {
      var links = document.getElementsByTagName("A");
      for (var i = 0; i < links.length; i++) {
        if (links[i].href.indexOf("#") != -1) {
          links[i].addEventListener("click", function(e) {
            console.debug("prevent href=# click");
            if (this.hash) {
              if (this.hash == "#") {
                e.preventDefault();
                return false;
              } else {
                /*
                    var el = document.getElementById(this.hash.replace(/#/, ""));
                    if (el) {
                      el.scrollIntoView(true);
                    }
                    */
              }
            }
            return false;
          });
        }
      }
    },
    false
  );



  function download_csv(csv, filename) {
    var csvFile;
    var downloadLink;

    // CSV FILE
    csvFile = new Blob([csv], {type: "text/csv"});

    // Download link
    downloadLink = document.createElement("a");

    // File name
    downloadLink.download = filename;

    // We have to create a link to the file
    downloadLink.href = window.URL.createObjectURL(csvFile);

    // Make sure that the link is not displayed
    downloadLink.style.display = "none";

    // Add the link to your DOM
    document.body.appendChild(downloadLink);

    // Lanzamos
    downloadLink.click();
}

function export_table_to_csv(html, filename) {
	var csv = [];
	var rows = document.querySelectorAll(".tappie-users-table tr");
	
    for (var i = 0; i < rows.length; i++) {
		var row = [], cols = rows[i].querySelectorAll("td, th");
		
        for (var j = 0; j < cols.length; j++) 
            row.push("\""+cols[j].innerText+"\"");
        
		csv.push(row.join(","));		
	}

    // Download CSV
    download_csv(csv.join("\n"), filename);
}

document.querySelector(".tappie-download-users-in-csv").addEventListener("click", function () {
    var html = document.querySelector(".tappie-users-table").outerHTML;
	export_table_to_csv(html, "subscribers.csv");
});