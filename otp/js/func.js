//			var baseurl = "http://robo.inflixer.com/";
var baseurl = "http://localhost/crwaling_bioskop/cpanel/";
// baseurl = "http://dev.motion.co.id/robo/v4/cpanel/";
// baseurl = "http://localhost/robo/v4/cpanel/";
//var baseurl = "http://devel.motion.co.id/crawling_bioskopkeren/";
var ajaxtable;
var getnewlinkurl   = baseurl + "crawler/getnewlink.php";
var cariurl         = baseurl + "php/searchTitle.php";
var pilihurl        = baseurl + "php/pilihmovie.php";
var fetchmovieurl   = baseurl + "php/fetchmovie.php";
var saveseasonurl   = baseurl + "php/saveseason.php";
var fetchepisodeurl = baseurl + "php/fetchepisode.php";
var ajaxurl         = baseurl + 'php/getinf.php';
var ajaxurlgetmovie = baseurl + 'php/getMovie.php';
var getEpisodeUrl   = baseurl + "php/getseriesepisode.php";
var getSeasonsUrl   = baseurl + "php/getseasons.php";
var type            = 'film';
var datapost        = '';
var marktitle       = "";


//gdrive anime
var ajaxurlgetmovieanime    = baseurl + 'php/gdrive/anime/getMovieAnime.php';
var ajaxurlgetserianime     = baseurl + 'php/gdrive/anime/getSeriAnime.php';
var cariurlanime            = baseurl + "php/gdrive/anime/searchTitleAnime.php";
var getEpisodeUrlAnime      = baseurl + "php/gdrive/anime/getSeriesEpisodeAnime.php";
var getSeasonsUrlAnime      = baseurl + "php/gdrive/anime/getSeasonsAnime.php";
var pilihurlanime           = baseurl + "php/gdrive/anime/pilihmovieanime.php";
var fetchmovieurlanime      = baseurl + "php/gdrive/anime/fetchmovieanime.php";
var saveseasonurlanime      = baseurl + "php/gdrive/anime/saveseasonanime.php";
var fetchepisodeurlanime    = baseurl + "php/gdrive/anime/fetchepisodeanime.php";
// var cariurlanimeseri = baseurl + "php/searchTitleAnimeSeri.php";

function alertSuccess(msg) {
    $.alert({
        icon:'fa fa-check',
        title:"Success",
        type:'green',
        content:msg
    });
}
function alertFail(msg) {
    $.alert({
        icon:'fa fa-exclamation',
        title:"Fail",
        type:'red',
        content:msg
    });
}

function changeType(id) {
    $.confirm({
        title: 'Confirm',
        icon: 'fa fa-exclamation-triangle',
        type: 'orange',
        content: "Ubah type menjadi seri? Pastikan title adalah seri!",
        buttons: {
            Ya: function () {
                $.post(baseurl+"php/changetype.php", { id: id}).done(function(data) {
                    console.log(data);
                    var res = JSON.parse(data);
                    if(res.code==1){
                        $("#myModal").modal('hide');
                        //ajaxtable.api().ajax.reload();
                        ajaxtable.fnDraw(false);
                        alertSuccess(res.msg||'Success');
                        //location.reload();
                    }else{
                        alertFail(res.msg||'error');
                    }
                });
            },
            cancel: function () {
                //close
            },
        }
    });

}

function pilihmovie(id,inflixerid,title,poster) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(pilihurl, { id: id, inflixerid: inflixerid , title:title,poster:poster,type:type}).done(function(data) {
                console.log(data);
                var res = JSON.parse(data);
                self.close();
                if(res.code==1){
                    $("#myModal").modal('hide');
                    //ajaxtable.api().ajax.reload();
                    ajaxtable.fnDraw(false);
                    alertSuccess(res.msg||'Success');
                    //location.reload();
                }else{
                    alertFail(res.msg||'error');
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });
};

function fetchmovie(id) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(fetchmovieurl, { id: id}).done(function(data) {
                console.log(data);
                var res = JSON.parse(data);
                self.close();
                if(res.code==1){
                    ajaxtable.fnDraw(false);
                    alertSuccess(res.msg||'Success');
                }else{
                    alertFail(res.msg||'error');
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });

        }
    });
}

function fetchepisode(id,season,episode) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(fetchepisodeurl, {id: id, season: season, episode: episode}).done(function (data) {
                var res = JSON.parse(data);
                self.setContent(res.msg);
                self.close();
                if(res.code==1){
                    self.setTitle("Success");
                    alertSuccess(res.msg || 'Success');
                    getlistepisode(id);

                }else{
                    alertFail(res.msg||'error');
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });
}

function fetchstatus(id,trx_id) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(baseurl + "php/fetchstatus.php", {trx_id: trx_id}).done(function (data) {
                var res = JSON.parse(data);
                self.setContent(res.msg);
                self.close();
                if(res.code==1){
                    self.setTitle("Success");
                    alertSuccess(res.msg || 'Success');
                    getlistepisode(id);

                }else{
                    alertFail(res.msg||'error');
                    if(res.code==-10){
                        getlistepisode(id);
                    }
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });
}

function fetchAllepisode(id) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: fetchepisodeurl,
                method: 'post',
                data:{ id: id }
            }).done(function(data) {
                console.log(data);
                var res = JSON.parse(data);
                self.setContent(res.msg);
                if(res.code==1){
                    self.setTitle("Success");
                    getlistepisode(id);
                }else{
                    self.setTitle("Fail");
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });
}

function submitlink() {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(pilihurl, { crawlerid: $("#table-id").val(), linkinflixer: $("#link-inflixer").val(), type:type }).done(function(data) {
                console.log(data);
                var res = JSON.parse(data);
                self.close();
                if(res.code==1){
                    $("#myModal").modal('hide');
                    //ajaxtable.api().ajax.reload();
                    ajaxtable.fnDraw(false);
                    alertSuccess(res.msg||'Success');

                    //location.reload();
                }else{
                    alertFail(res.msg||'error');
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });
}

function editSeason(id,titleid) {
    var submitType = $('#btnSubmitSeason_'+id).val();
    if(submitType==0){
        $('#txtseason_'+id).prop('readonly',false).focus();
        $('#btnSubmitSeason_'+id).val(1);
        $('#btnSubmitSeason_'+id).html('Save');
    }else if(submitType==1){
        var numseason = $('#txtseason_'+id).val();
        $.post(saveseasonurl, { id: id, numseason:numseason }).done(function(data) {
            console.log(data);
            var res = JSON.parse(data);
            if(res.code==1){
                alertSuccess(res.msg||'Success');
                getlistepisode(titleid);
            }else{
                alertFail(res.msg||'error');
            }
        });

        $('#txtseason_'+id).prop('readonly',true);
        $('#btnSubmitSeason_'+id).val(0);
        $('#btnSubmitSeason_'+id).html('Edit');
    }
}

function editConf(id) {
    var submitType = $('#btnSubmitConf_'+id).val();
    if(submitType==0){
        $('#txtConf_'+id).prop('readonly',false).focus();
        $('#btnSubmitConf_'+id).val(1);
        $('#btnSubmitConf_'+id).html('Save');
    }else if(submitType==1){
        var val = $('#txtConf_'+id).val();
        $.post(baseurl + "php/saveConf.php", { key: id, val:val }).done(function(data) {
            console.log(data);
            var res = JSON.parse(data);
            if(res.code==1){
                alertSuccess(res.msg||'Success');
            }else{
                alertFail(res.msg||'error');
            }
        }).fail(function (e) {
            alertFail("Request failed");
        });

        $('#txtConf_'+id).prop('readonly',true);
        $('#btnSubmitConf_'+id).val(0);
        $('#btnSubmitConf_'+id).html('Edit');
    }
}

function detailMovie(id,rtitle,ntitle,poster,link,tahun,director,t) {
    $('#link').val("");
    $('#subtitle').val("");
    $('#video').html("");
    $('#judulfilm').html("");
    $('#Posterfilm').html("");
    $('#link-inflixer').val("");
    $('#season').html("");
    $('#episode').html("");
    $('#judulfilmbioskopkeren').html("");
    $('#list').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    $('#table-id').val(id);
    $("#list-info").hide();
    marktitle = ntitle;
    // $.post( cariurl, { id:id ,type:t}).done(function(data) {
    //     $('#list').html(data);
    //     //alert(data);
    // }).fail(function() {
    //     $('#list').html("Gagal melakukan pencarian");
    // });
    
    searchTitle(id,t,1);
    if(t=='seri'){
        getlistseason(id);
        getlistepisode(id);
    }

    var link_bk = "<a href='"+link+"'><b>"+rtitle+"</b></a>"
    $('#judulfilmbioskopkeren').html(link_bk);
    $('#judulfilm').html("<b>"+ntitle+"</b>");
    $('#tahunFilm').html("<b>"+tahun+"</b>");
    $('#director').html("<b>"+director+"</b>");
    $('#Posterfilm').html("<img src='"+poster+"'/>");
}

function searchTitle(id,t,page) {
    $('#list').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    $.post( cariurl, { id:id ,type:t,page:page}).done(function(data) {
        if(data.total!=null){
            $("#btn-prev").prop("disabled",false);
            $("#list-info").show();
            $("#btn-next").prop("disabled",false);
            
            if(page==1){
                $("#btn-prev").prop("disabled",true);
            }
            
            if(page == data.total_pages){
                $("#btn-next").prop("disabled",true);
            }
            $("#btn-next").attr("onclick","searchTitle("+id+",'"+t+"',"+(page+1)+")");
            $("#btn-prev").attr("onclick","searchTitle("+id+",'"+t+"',"+(page-1)+")");
            
            $('#list-info-total').html("Total:" + data.total + ", page:" + data.page + " of "+ data.total_pages);

            $('#list').html(data.data);
            $("#list").mark(marktitle);
        }else{
            $('#list').html("");
        }
        
        
        //alert(data);
    }).fail(function() {
        $('#list').html("Gagal melakukan pencarian");
    });
}

function confirmpilih(id,inflixerid,title,poster) {
    $.confirm({
        title: 'Confirm',
        icon: 'fa fa-exclamation-triangle',
        type: 'orange',
        content: "Pastikan film sudah sesuai, lanjutkan fetch?",
        buttons: {
            Ya: function () {
                pilihmovie(id,inflixerid,title,poster);
            },
            cancel: function () {
                //close
            },
        }
    });
}

function getlistseason(id) {
    $.get( getSeasonsUrl, { id:id }).done(function(data) {
        $('#season').html(data);
        //alert(data);
    }).fail(function() {
        $('#season').html("Gagal menampilkan list episode");
    });
}

function getlistepisode(id) {
    $.get( getEpisodeUrl, { id:id }).done(function(data) {
        $('#episode').html(data);
        //alert(data);
    }).fail(function() {
        $('#episode').html("Gagal melakukan pencarian");
    });
}


// Gdrive anime
function detailMovieAnime(id,rtitle,ntitle,poster,link,tahun,director,t) {
    $('#link').val("");
    $('#subtitle').val("");
    $('#video').html("");
    $('#judulfilm').html("");
    $('#Posterfilm').html("");
    $('#link-inflixer').val("");
    $('#season').html("");
    $('#episode').html("");
    $('#judulfilmbioskopkeren').html("");
    $('#list').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    $('#table-id').val(id);
    // $('#btnsubmitlink').html("");
    // $('#btnsubmitlink').html('<button type="submit" class="btn btn-success " onclick="submitlinkanime()" id="btnsubmitlink">Submit</button>');
    // $('#btnsubmitlink').val("");

    marktitle = ntitle;
    // $.post( cariurl, { id:id ,type:t}).done(function(data) {
    //     $('#list').html(data);
    //     //alert(data);
    // }).fail(function() {
    //     $('#list').html("Gagal melakukan pencarian");
    // });

    searchTitleAnime(id,t,1);
    if(t=='seri'){
        getlistseasonanime(id);
        getlistepisodeanime(id);
    }

    var link_bk = "<a href='"+link+"'><b>"+rtitle+"</b></a>"
    $('#judulfilmbioskopkeren').html(link_bk);
    $('#judulfilm').html("<b>"+ntitle+"</b>");
    $('#tahunFilm').html("<b>"+tahun+"</b>");
    $('#director').html("<b>"+director+"</b>");
    $('#Posterfilm').html("<img src='"+poster+"' width='125px'/>");
}

function searchTitleAnime(id,t,page) {
    $('#list').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    $.post( cariurlanime, { id:id ,type:t,page:page}).done(function(data) {
        // $('#list').html(data);
        //alert(data);
        if(data.total!=null){
            $("#btn-prev").prop("disabled",false);
            $("#list-info").show();
            $("#btn-next").prop("disabled",false);

            if(page==1){
                $("#btn-prev").prop("disabled",true);
            }

            if(page == data.total_pages){
                $("#btn-next").prop("disabled",true);
            }
            $("#btn-next").attr("onclick","searchTitleAnime("+id+",'"+t+"',"+(page+1)+")");
            $("#btn-prev").attr("onclick","searchTitleAnime("+id+",'"+t+"',"+(page-1)+")");

            $('#list-info-total').html("Total:" + data.total + ", page:" + data.page + " of "+ data.total_pages);

            $('#list').html(data.data);
            $("#list").mark(marktitle);
        }else{
            $('#list').html("");
        }
    }).fail(function() {
        $('#list').html("Gagal melakukan pencarian");
    });
}

function getlistseasonanime(id) {
    $.get( getSeasonsUrlAnime, { id:id }).done(function(data) {
        $('#season').html(data);
        //alert(data);
    }).fail(function() {
        $('#season').html("Gagal menampilkan list episode");
    });
}

function getlistepisodeanime(id) {
    $.get( getEpisodeUrlAnime, { id:id }).done(function(data) {
        $('#episode').html(data);
        //alert(data);
    }).fail(function() {
        $('#episode').html("Gagal melakukan pencarian");
    });
}

function pilihmovieanime(id,inflixerid,title,poster) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: pilihurlanime,
                method: 'post',
                data:{ id: id, inflixerid: inflixerid , title:title,poster:poster,type:type}
            }).done(function(data) {
                console.log(data);
                var res = JSON.parse(data);
                self.setContent(res.msg);
                if(res.code==1){
                    self.setTitle("Success");
                }else{
                    self.setTitle("Fail");
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });

    // $.post(pilihurlanime, { id: id, inflixerid: inflixerid , title:title,poster:poster,type:type}).done(function(data) {
    //     console.log(data);
    //     var res = JSON.parse(data);
    //     if(res.code==1){
    //         $("#myModal").modal('hide');
    //         //ajaxtable.api().ajax.reload();
    //         ajaxtable.fnDraw(false);
    //         alertSuccess(res.msg||'Success');
    //         //location.reload();
    //     }else{
    //         alertFail(res.msg||'error');
    //     }
    // });
};

function submitlinkanime() {
    $.post(pilihurlanime, { crawlerid: $("#table-id").val(), linkinflixer: $("#link-inflixer").val(), type:type }).done(function(data) {
        console.log(data);
        var res = JSON.parse(data);
        if(res.code==1){
            $("#myModal").modal('hide');
            //ajaxtable.api().ajax.reload();
            ajaxtable.fnDraw(false);
            alertSuccess(res.msg||'Success');

            //location.reload();
        }else{
            alertFail(res.msg||'error');
        }
    });
}

function fetchmovieanime(id) {
    $.post(fetchmovieurlanime, { id: id}).done(function(data) {
        console.log(data);
        var res = JSON.parse(data);
        if(res.code==1){
//                $("#myModal").modal('hide');
            //ajaxtable.api().ajax.reload();
//                ajaxtable.fnDraw(false);
            ajaxtable.fnDraw(false);
            alertSuccess(res.msg||'Success');
            //location.reload();
        }else{
            alertFail(res.msg||'error');
        }
    });
}

function unlinkMovieAnime(id,type) {
    $.post(baseurl+"php/gdrive/anime/unlink.php", { id: id,type:type}).done(function(data) {
        console.log(data);
        var res = JSON.parse(data);
        if(res.code==1){
//                $("#myModal").modal('hide');
            //ajaxtable.api().ajax.reload();
//                ajaxtable.fnDraw(false);
            ajaxtable.fnDraw(false);
            alertSuccess(res.msg||'Success');
            //location.reload();
        }else{
            alertFail(res.msg||'error');
        }
    });
}

function unlinkMovie(id) {
    $.post(baseurl+"php/unlink.php", { id: id}).done(function(data) {
        console.log(data);
        var res = JSON.parse(data);
        if(res.code==1){
//                $("#myModal").modal('hide');
            //ajaxtable.api().ajax.reload();
//                ajaxtable.fnDraw(false);
            ajaxtable.fnDraw(false);
            alertSuccess(res.msg||'Success');
            //location.reload();
        }else{
            alertFail(res.msg||'error');
        }
    });
}

function editSeasonAnime(id,titleid) {
    var submitType = $('#btnSubmitSeason').val();
    if(submitType==0){
        $('#txtseason_'+id).prop('readonly',false).focus();
        $('#btnSubmitSeason').val(1);
        $('#btnSubmitSeason').html('Save');
    }else if(submitType==1){
        var numseason = $('#txtseason_'+id).val();
        $.post(saveseasonurlanime, { id: id, numseason:numseason }).done(function(data) {
            console.log(data);
            var res = JSON.parse(data);
            if(res.code==1){
                alertSuccess(res.msg||'Success');
                // getlistepisode(titleid);
            }else{
                alertFail(res.msg||'error');
            }
        });

        $('#txtseason_'+id).prop('readonly',true);
        $('#btnSubmitSeason').val(0);
        $('#btnSubmitSeason').html('Edit');
    }
}

function fetchepisodeanime(id,season,episode) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(fetchepisodeurlanime, {id: id, season: season, episode: episode}).done(function (data) {
                console.log(data);
                var res = JSON.parse(data);
                self.setContent(res.msg);
                if(res.code==1){
                    self.setTitle("Success");
                    getlistepisodeanime(id);
                }else{
                    self.setTitle("Fail");
                }
            });
        }
    });

}

function fetchAllepisodeanime(id) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: fetchepisodeurlanime,
                method: 'post',
                data:{ id: id }
            }).done(function(data) {
                console.log(data);
                var res = JSON.parse(data);
                self.setContent(res.msg);
                if(res.code==1){
                    self.setTitle("Success");
                    getlistepisodeanime(id);
                }else{
                    self.setTitle("Fail");
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });
}

function detailWiki(id) {
    $.post( baseurl + "php/asianwiki/getDetail.php", { id:id }).done(function(data) {
        $('#wikiPlot').html(data.plot);
        $('#wikiNotes').html(data.notes);
        $('#wikiTitle').html(data.rawtitle);
        $('#wikiPoster').attr("src",data.poster);
        $('#wikiRating').html(data.rating);
        $('#wikiNumVotes').html("("+ data.num_votes + "votes)");
        $('#wikiType').html(data.type);
        var content = "";
        var profiles = data.profile;
        for (key in profiles){
            content += "<li> <b class='ucwords'>"+key.replace("_"," ")+":</b> "+ profiles[key]+"</li>";
        }
        $('#wikiProfiles').html(content);

    }).fail(function() {
        $('#wikiPlot').html("");
    });
    getWikiCasts(id);
    getWikiEpisodes(id);
}

function getWikiCasts(id) {
    $('#wikiCasts').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    $.post( baseurl + "php/asianwiki/getCasts.php", { id:id }).done(function(data) {
        $('#wikiCasts').html(data);
    }).fail(function() {
        $('#wikiCasts').html("Gagal menampilkan cast");
    });
}

function getWikiEpisodes(id) {
    $('#wikiEpisodes').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    $.post( baseurl + "php/asianwiki/getEpisodes.php", { id:id }).done(function(data) {
        $('#wikiEpisodes').html(data);
    }).fail(function() {
        $('#wikiEpisodes').html("Gagal menampilkan Episode");
    });
}