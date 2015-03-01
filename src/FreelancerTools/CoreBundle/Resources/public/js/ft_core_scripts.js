//delete modal -- caller must be a form
$('.deleteButton').on('click', function (e) {
    e.preventDefault();
    var item = $(this);
    var form = $(this).closest('form');
    $('#deleteModal').modal('show');
    $('.confirmDelete').on("click", function () {
        form.trigger("submit");
    });
});

