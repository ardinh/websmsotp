
function detailMovieDramaqu(id,rtitle,ntitle,poster,link,tahun,director,t) {
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
    
    searchTitleDramaqu(id,t,1);
    if(t=='seri'){
        getlistepisodeDramaqu(id);
    }

    var link_bk = "<a href='"+link+"'><b>"+rtitle+"</b></a>"
    $('#judulfilmbioskopkeren').html(link_bk);
    $('#judulfilm').html("<b>"+ntitle+"</b>");
    $('#tahunFilm').html("<b>"+tahun+"</b>");
    $('#director').html("<b>"+director+"</b>");
    $('#Posterfilm').html("<img src='"+poster+"'/>");
}

function searchTitleDramaqu(id,t,page) {
    $('#list').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    $.post( baseurl + "php/dramaqu/searchTitle.php", { id:id ,type:t,page:page}).done(function(data) {
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
            $("#btn-next").attr("onclick","searchTitleDramaqu("+id+",'"+t+"',"+(page+1)+")");
            $("#btn-prev").attr("onclick","searchTitleDramaqu("+id+",'"+t+"',"+(page-1)+")");
            
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

function getlistepisodeDramaqu(id) {
    $.get( baseurl + "php/dramaqu/getseriesepisode.php", { id:id }).done(function(data) {
        $('#episode').html(data);
        //alert(data);
    }).fail(function() {
        $('#episode').html("Gagal melakukan pencarian");
    });
}

function pilihmovieDramaqu(id,inflixerid,title,poster) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(baseurl + "php/dramaqu/pilihmovie.php", { id: id, inflixerid: inflixerid , title:title,poster:poster,type:type}).done(function(data) {
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

function fetchmovieDramaqu(id) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(baseurl + "php/dramaqu/fetchmovie.php", { id: id}).done(function(data) {
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

function fetchepisodeDramaqu(id,season,episode) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(baseurl + "php/dramaqu/fetchepisode.php", {id: id, season: season, episode: episode}).done(function (data) {
                var res = JSON.parse(data);
                self.setContent(res.msg);
                self.close();
                if(res.code==1){
                    self.setTitle("Success");
                    alertSuccess(res.msg || 'Success');
                    getlistepisodeDramaqu(id);

                }else{
                    alertFail(res.msg||'error');
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });
}

function fetchstatusDramaqu(id,trx_id) {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(baseurl + "php/dramaqu/fetchstatus.php", {trx_id: trx_id}).done(function (data) {
                var res = JSON.parse(data);
                self.setContent(res.msg);
                self.close();
                if(res.code==1){
                    self.setTitle("Success");
                    alertSuccess(res.msg || 'Success');
                    getlistepisodeDramaqu(id);

                }else{
                    alertFail(res.msg||'error');
                    if(res.code==-10){
                        getlistepisodeDramaqu(id);
                    }
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });
}

function fetchAllepisodeDramaqu(id) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: baseurl + "php/dramaqu/fetchepisode.php",
                method: 'post',
                data:{ id: id }
            }).done(function(data) {
                console.log(data);
                var res = JSON.parse(data);
                self.setContent(res.msg);
                if(res.code==1){
                    self.setTitle("Success");
                    getlistepisodeDramaqu(id);
                }else{
                    self.setTitle("Fail");
                }
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
    });
}

function submitlinkDramaqu() {
    $.confirm({
        content: function () {
            var self = this;
            return $.post(baseurl + "php/dramaqu/pilihmovie.php", { crawlerid: $("#table-id").val(), linkinflixer: $("#link-inflixer").val(), type:type }).done(function(data) {
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

function unlinkMovieDramaqu(id) {
    $.post(baseurl+"php/dramaqu/unlink.php", { id: id}).done(function(data) {
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