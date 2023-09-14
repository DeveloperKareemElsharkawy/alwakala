$('.sa-warning').click(function (e) {
    var id = $(this).attr('object_id');
    var d_url = $(this).attr('delete_url');
    var button_type = $(this).attr('button_type');
    if(button_type == 'restore'){
        var title = 'استرجاع هذا العنصر من الارشيف';
        var text = 'هل انت متأكد انك تريد استرجاع هذا العنصر الى الارشيف !';
    }
    if(button_type == 'delete'){
        var title = 'حذف هذا العنصر  ';
        var text = 'هل انت متأكد انك تريد حذف هذا العنصر !';
    }
    if(button_type == 'archive'){
        var title = 'ارشفة هذا العنصر ';
        var text = 'هل انت متأكد انك تريد ارسال هذا العنصر الى الارشيف !';
    }
    Swal.fire({
        title: title,
        text: text,
        icon: "",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-gradient w-xs me-2 mt-2",
        cancelButtonClass: "btn btn-light w-xs mt-2",
        confirmButtonText: "نعم متأكد",
        cancelButtonText: "لا",
        buttonsStyling: !1,
        showCloseButton: false
    }).then(function (t) {
        if(t['isConfirmed']){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                type: button_type == 'delete' ? 'delete' : 'get',
                url: '/admin_panel/' + d_url + id,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success : function (t) {
                    if (t.type == 'success') {
                        $('.image_class' + id).remove();
                    }
                    Swal.fire({
                        title: t.title,
                        text: t.msg,
                        icon: t.type,
                        confirmButtonClass: "btn btn-gradient w-xs me-2 mt-2",
                        confirmButtonText: "تم",
                        buttonsStyling: !1
                    })
                }
            });
        }else{
            Swal.fire({
                title: 'تم الايقاف',
                text: 'لم يتم اكمال العملية :)',
                icon: '',
                confirmButtonClass: "btn btn-gradient w-xs me-2 mt-2",
                confirmButtonText: "اغلاق",
                buttonsStyling: !1
            })
            e.preventDefault();
        }
    })
});
