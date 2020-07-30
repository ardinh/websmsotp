
function detailMovieJF(id,rtitle,ntitle,poster,link,tahun,director,t) {
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
    
    searchTitleJF(id,t,1);
    if(t=='seri'){
        getlistseasonJF(id);
        getlistepisodeJF(id);
    }

    var link_bk = "<a href='"+link+"'><b>"+rtitle+"</b></a>"
    $('#judulfilmbioskopkeren').html(link_bk);
    $('#judulfilm').html("<b>"+ntitle+"</b>");
    $('#tahunFilm').html("<b>"+tahun+"</b>");
    $('#director').html("<b>"+director+"</b>");
    $('#Posterfilm').html("<img src='"+poster+"'/>");
}

function searchTitleJF(id,t,page) {
    $('#list').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    $.post( baseurl + "php/jf/searchTitle.php", { id:id ,type:t,page:page}).done(function(data) {
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
            $("#btn-next").attr("onclick","searchTitleJF("+id+",'"+t+"',"+(page+1)+")");
            $("#btn-prev").attr("onclick","searchTitleJF("+id+",'"+t+"',"+(page-1)+")");
            
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

function editSeasonJF(id,titleid) {
    var submitType = $('#btnSubmitSeason_'+id).val();
    if(submitType==0){
        $('#txtseason_'+id).prop('readonly',false).focus();
        $('#btnSubmitSeason_'+id).val(1);
        $('#btnSubmitSeason_'+id).html('Save');
    }else if(submitType==1){
        var numseason = $('#txtseason_'+id).val();
        $.post(baseurl + "php/jf/saveseason.php", { id: id, numseason:numseason }).done(function(data) {
            console.log(data);
            var res = JSON.parse(data);
            if(res.code==1){
                alertSuccess(res.msg||'Success');
                getlistepisodeJF(titleid);
            }else{
                alertFail(res.msg||'error');
            }
        });

        $('#txtseason_'+id).prop('readonly',true);
        $('#btnSubmitSeason_'+id).val(0);
        $('#btnSubmitSeason_'+id).html('Edit');
    }
}

function getlistseasonJF(id) {
    $.get( baseurl + "php/jf/getseasons.php", { id:id }).done(function(data) {
        $('#season').html(data);
        //alert(data);
    }).fail(function() {
        $('#season').html("Gagal menampilkan list episode");
    });
}

function getlistepisodeJF(id) {
    $.get( baseurl + "php/jf/getseriesepisode.php", { id:id }).done(function(data) {
        $('#episode').html(data);
        //alert(data);
    }).fail(function() {
        $('#episode').html("Gagal melakukan pencarian");
    });
}

function pilihmovieJF(id,inflixerid,title,poster) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(baseurl + "php/jf/pilihmovie.php", { id: id, inflixerid: inflixerid , title:title,poster:poster,type:type}).done(function(data) {
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

function fetchmovieJF(id) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(baseurl + "php/jf/fetchmovie.php", { id: id}).done(function(data) {
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

function fetchepisodeJF(id,season,episode) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(baseurl + "php/jf/fetchepisode.php", {id: id, season: season, episode: episode}).done(function (data) {
                var res = JSON.parse(data);
                self.setContent(res.msg);
                self.close();
                if(res.code==1){
                    self.setTitle("Success");
                    alertSuccess(res.msg || 'Success');
                    getlistepisodeJF(id);

                }else{
                    alertFail(res.msg||'error');
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });
}

function fetchstatusJF(id,trx_id,type) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(baseurl + "php/jf/fetchstatus.php", {trx_id: trx_id,type:type}).done(function (data) {
                var res = JSON.parse(data);
                self.setContent(res.msg);
                self.close();
                if(res.code==1){
                    self.setTitle("Success");
                    alertSuccess(res.msg || 'Success');
                    if(type=='film'){
                        ajaxtable.fnDraw(false);
                    }else{
                        getlistepisodeJF(id);
                    }


                }else{
                    alertFail(res.msg||'error');
                    if(res.code==-10){
                        if(type=='film'){
                            ajaxtable.fnDraw(false);
                        }else{
                            getlistepisodeJF(id);
                        }
                    }
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });
}

function fetchAllepisodeJF(id) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: baseurl + "php/jf/fetchepisode.php",
                method: 'post',
                data:{ id: id }
            }).done(function(data) {
                console.log(data);
                var res = JSON.parse(data);
                self.setContent(res.msg);
                if(res.code==1){
                    self.setTitle("Success");
                    getlistepisodeJF(id);
                }else{
                    self.setTitle("Fail");
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });
}

function submitlinkJF() {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(baseurl + "php/jf/pilihmovie.php", { crawlerid: $("#table-id").val(), linkinflixer: $("#link-inflixer").val(), type:type }).done(function(data) {
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

function unlinkMovieJF(id) {
    $.post(baseurl+"php/jf/unlink.php", { id: id}).done(function(data) {
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