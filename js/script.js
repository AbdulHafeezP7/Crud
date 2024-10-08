// function for pagination
function pagination(totalpages,currentpages){
    var pagelist="";
    if(totalpages>1){
        currentpages=parseInt(currentpages);
        pagelist +=`<ul class="pagination justify-content-center">`;
        const prevClass=currentpages==1?"disabled":"";
        pagelist +=`<li class="page-item ${prevClass}"><a class="page-link" href="#" data-page="${currentpages-1}">Previous</a></li>`;
        for(let p=1;p<=totalpages;p++){
            const activeClass=currentpages==p?"active":"";
            pagelist +=`<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
        }
        const nextClass=currentpages==totalpages?"disabled":"";
        pagelist +=` <li class="page-item ${nextClass}"><a class="page-link" href="#" data-page="${currentpages+1}">Next</a></li>`;
        pagelist +=`</ul>`;
    }
    $("#pagination").html(pagelist);
}

// function for display messages
function showMessage(message, type) {
    const messageDiv = $("#message");
    messageDiv.removeClass('alert-success alert-danger alert-info');
    messageDiv.addClass(`alert-${type}`);
    messageDiv.text(message);
    messageDiv.show();
    setTimeout(() => {
        messageDiv.hide();
    }, 3000);
}

// Function to get player row
function getplayerrow(player) {
    var playerRow = "";
    if (player) {
        playerRow = `<tr>
            <td><img src="uploads/${player.player_photo}" alt="${player.player_name}" width="50"></td>
            <td>${player.player_name}</td>
            <td>${player.player_email}</td>
            <td>${player.player_date_of_birth}</td>
            <td>${player.player_age}</td>
            <td>${player.player_nationality}</td>
            <td>${player.player_position}</td>
            <td>${player.player_height}</td>
            <td>${player.player_weight}</td>
            <td>
                <a href="#" class="profile" data-toggle="modal" data-target="#playerViewModal" data-id="${player.player_id}" title="View Player Profile"><i class="fas fa-eye mr-2 text-success"></i></a>
                <a href="#" class="edit" data-toggle="modal" data-target="#playerModal" data-id="${player.player_id}" title="Edit Player Profile"><i class="fas fa-edit mr-2 text-info"></i></a>
                <a href="#" class="delete" data-id="${player.player_id}" title="Delete Player Profile"><i class="fas fa-trash-alt mr-2 text-danger"></i></a>
            </td>
        </tr>`;
    }
    return playerRow;
}

// Function to get players
function getPlayers() {
    var pageno = $("#currentpage").val();
    $.ajax({
        url: "ajax.php",
        type: "GET",
        dataType: "json",
        data: { page: pageno, action: 'getallplayers' },
        beforeSend: function () {
            console.log("Wait....Data is loading..");
        },
        success: function (rows) {
            console.log(rows);
            if (rows.players) {
                var playerslist = "";
                $.each(rows.players, function (index, player) {
                    playerslist += getplayerrow(player); 
                });
                $("#playertable tbody").html(playerslist);
                let totalplayers=rows.count; 
                let totalpages=Math.ceil(parseInt(totalplayers)/4);
                const currentpages=$("#currentpage").val();
                pagination(totalpages,currentpages);
            }
        },
        error: function (request, error) {
            console.log(arguments);
            console.log("Error " + error);
            console.log(request.responseText);
        },
    });
}

// Function to search players
function searchPlayers(query) {
    $.ajax({
        url: "ajax.php",
        type: "GET",
        dataType: "json",
        data: { searchQuery: query, action: 'searchPlayers' },
        beforeSend: function () {
            console.log("Searching players...");
        },
        success: function (rows) {
            console.log(rows);
            if (rows.players) {
                var playerslist = "";
                $.each(rows.players, function (index, player) {
                    playerslist += getplayerrow(player);
                });
                $("#playertable tbody").html(playerslist);
                let totalplayers = rows.count;
                let totalpages = Math.ceil(parseInt(totalplayers) / 4);
                const currentpages = $("#currentpage").val();
                pagination(totalpages, currentpages);

                if (rows.players.length === 0) {
                    showMessage('No players found.', 'info');
                }
            }
        },
        error: function (request, error) {
            console.log(arguments);
            console.log("Error " + error);
            console.log(request.responseText);
        },
    });
}

// Document ready function
$(document).ready(function () {
    // Adding players
    $(document).on("submit", "#addform", function (e) {
        e.preventDefault();
        
        // Append action to FormData
        var formData = new FormData(this);
        formData.append('action', 'addPlayer');
    
        // AJAX
        $.ajax({
            url: "ajax.php",
            type: "POST",
            dataType: "json",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                console.log("Wait....Data is loading..");
            },
            success: function (response) {
                console.log(response);
                if (response.error) {
                    console.log('Error: ' + response.error);
                    showMessage('Error adding player: ' + response.error, 'danger');
                } else {
                    $("#playerModal").modal("hide");
                    $("#addform")[0].reset();
                    getPlayers(); 
                    showMessage('Player added successfully!', 'success');
                }
            },            
            error: function (request, error) {
                console.log(arguments);
                console.log("Error " + error);
                console.log(request.responseText);
            },
        });
    });    

    // on click event for pagination
    $(document).on("click","ul.pagination li a" ,function (event) {
        event.preventDefault();

        const pagenum=$(this).data("page");
        $("#currentpage").val(pagenum);
        getPlayers();
        $(this).parent().siblings().removeClass("active");
        $(this).parent().addClass("active");
    })

    // on click event for editing players profile
$(document).on("click", "a.edit", function () {
    var pid = $(this).data("id");
    $.ajax({
        url: "ajax.php",
        type: "GET",
        dataType: "json",
        data: { player_id: pid, action: 'editplayers' },
        beforeSend: function () {
            console.log("Wait....Data is loading..");
        },
        success: function (player) {
            console.log(player);
            if (player.error) {
                console.log('Error: ' + player.error);
            } else {
                $("#playerName").val(player.player_name);
                $("#playerEmail").val(player.player_email);
                $("#playerDateOfBirth").val(player.player_date_of_birth);
                $("#playerAge").val(player.player_age);
                $("#playerNationality").val(player.player_nationality);
                $("#playerPosition").val(player.player_position);
                $("#playerHeight").val(player.player_height);
                $("#playerWeight").val(player.player_weight);
                $("#playerId").val(player.player_id);
                $("#currentPhoto").val(player.player_photo);
                $("#playerModal").modal("show");
            }
        },
        error: function (request, error) {
            console.log(arguments);
            console.log("Error " + error);
            console.log(request.responseText);
        },
    });
});
    
    // on click for adding player btn
    $("#addplayerbtn").on("click",function(){
        $("#addform")[0].reset();
        $("#playerId").val("");
    });

    // on click for deleting player data
    $(document).on("click", "a.delete", function (event) {
        event.preventDefault();
    
        var pid = $(this).data("id");
    
        if (confirm("Are you sure you want to delete this player?")) {
            $.ajax({
                url: "ajax.php",
                type: "POST",
                dataType: "json",
                data: { player_id: pid, action: 'deletePlayer' },
                success: function (response) {
                    if (response.success) {
                        alert("Player deleted successfully.");
                        getPlayers();
                        showMessage('Player deleted successfully!', 'success');
                    } else {
                        alert("Failed to delete player: " + response.error);
                        showMessage('Error deleting player: ' + response.error, 'danger');
                    }
                },                
                error: function (request, error) {
                    console.log(arguments);
                    console.log("Error " + error);
                    console.log(request.responseText);
                },
            });
        }
    });
      
   // on click for viewing player profile
    $(document).on("click", "a.profile", function () {
        var pid = $(this).data("id");

        $.ajax({
            url: "ajax.php",
            type: "GET",
            dataType: "json",
            data: { player_id: pid, action: 'viewPlayerProfile' },
            success: function (player) {
                if (player) {
                    const profile = `
                        <div class="row">
                            <div class="col-6 col-md-4">
                                <img src="uploads/${player.player_photo}" alt="${player.player_name}" class="rounded">
                            </div>
                            <div class="col-6 col-md-8">
                                <h4 class="text-primary"> ${player.player_name}</h4>
                                <p>
                                <i class="fas fa-envelope"></i>${player.player_email}
                                <br>
                                <i class="fas fa-calendar-alt"></i>${player.player_date_of_birth}
                                <br>
                                <i class="fas fa-birthday-cake"></i>${player.player_age}
                                <br>
                                <i class="fas fa-flag"></i>${player.player_nationality}
                                <br>
                                <i class="fas fa-futbol"></i>${player.player_position}
                                <br>
                                <i class="fas fa-arrows-alt-v"></i>${player.player_height}
                                <br>
                                <i class="fas fa-weight"></i>${player.player_weight}
                                </p>
                            </div>
                        </div>
                    `;
                    $("#profile").html(profile);
                    $("#playerViewModal").modal("show");
                } else {
                    console.log('Error: ' + player.error);
                }
            },
            error: function (request, error) {
                console.log(arguments);
                console.log("Error " + error);
                console.log(request.responseText);
            }
        });
    });

     // Search button click event
     $("#searchBtn").on("click", function () {
        var query = $("#searchQuery").val();
        if (query) {
            searchPlayers(query);
        } else {
            getPlayers();
        }
    });

    // Search input keyup event
    $("#searchQuery").on("keyup", function () {
        var query = $(this).val();
        if (query) {
            searchPlayers(query);
        } else {
            getPlayers();
        }
    });

    // Calling getPlayers function on page load
    getPlayers();
});
