    function showMessageModal(status, message) {
        var modal = $('#messageModal');
    
        modal.find('.modal-body').text(message);
    
        if (status === 'success') {
            modal.find('.modal-content').removeClass('modal-danger').addClass('modal-success');
        } else {
            modal.find('.modal-content').removeClass('modal-success').addClass('modal-danger');
        }
    
        modal.modal('show');
    
        modal.on('hidden.bs.modal', function () {
            location.reload();
        });
    }
    
    function submitForm() {
        var phoneValue = $('#phone').val();
        var emailValue = $('#email').val();
    
        var formData = {
            phone: phoneValue,
            email: emailValue
        };
    
        $.ajax({
            type: 'POST',
            url: 'http://restapi/api/create.php',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                if (response.status === 'success') {
                    showMessageModal(response.status, response.message);
                } else {
                    showMessageModal('error', response.message);
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                var errorMessage;
            
                try {
                    var response = JSON.parse(xhr.responseText);
                    errorMessage = response.message;
                } catch (e) {
                    errorMessage = 'Error sending request.';
                }
            
                showMessageModal('error', errorMessage);
            }
        });
    }
    