$('#content').hide()
$('#loading').hide()
$('#toast').hide()

const setActiveLink = (element) => {
    $('#navbarCollapse .nav-link').removeClass('active')
    $(`#navbarCollapse .nav-link:${element}`).addClass('active')
}

const loading = (show) => {
    if (show) {
        $('#loading').show()
        $('#content').hide()
    } else {
        $('#loading').hide()
        $('#content').show()
    }
}

const toast = (title, message, className) => {
    $('#toast').show()

    $('#toast .title').text(title)
    $('#toast .toast-body').html(message)
    $('#toast .toast-header').toggleClass(className)
}

const modal = (contact) => {
    const modalQuestion = new bootstrap.Modal('#modal-question')

    modalQuestion.show()

    $('#modal-question .modal-body span').text(`${contact.firstname} ${contact.lastname}`)

    $('#btn-delete-contact').on('click', function () {
        deleteContact(contact.id, function () {
            modalQuestion.hide()

            loadContacts()
        })
    })
}

const preventSubmit = (event) => {
    event.preventDefault()
    event.stopPropagation()
}

const validateForm = () => {
    const forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                preventSubmit(event)
            }

            $(form).addClass('was-validated')
        }, false)
    })
}

const formatData = () => {
    return JSON.stringify({
        firstname: $('#firstname').val(),
        lastname: $('#lastname').val(),
        email: $('#email').val(),
        phone: $('#phone').val(),
    })
}

const cleanForm = () => {
    $('#content form').removeClass('was-validated')

    setTimeout(() => {
        $('#firstname').val('')
        $('#lastname').val('')
        $('#email').val('')
        $('#phone').val('')
    }, 200)
}

const validForm = () => {
    return !(!$('#firstname').val() ||
        !$('#lastname').val() ||
        !$('#email').val() ||
        !$('#phone').val())
}

const checkResponse = (response, callback = undefined) => {
    if (response.status) {
        toast('Success', response.message, 'text-bg-success')

        cleanForm()

        if (typeof callback === 'function') {
            callback()
        }
    } else {
        toast('Error', response.message, 'text-bg-danger')
    }
}

const loadContacts = () => {
    $.ajax({
        url: '/api/contacts',
        dataType: 'json',
        contentType: 'application/json',
        type: 'GET',
        error: function (jqXHR, textStatus, errorThrown) {
            toast('Error', jqXHR + " " + textStatus + " " + errorThrown, 'text-bg-danger')
        },
        beforeSend: function () {
            loading(true);

            $('#content tbody').html('')
        },
        success: function (response) {
            if (response.status) {
                $.each(response.data, function (index, item) {
                    $('#content tbody').append(`
                <tr id="contact-${item.id}">
                  <th scope="row">${item.id}</th>
                  <td>${item.firstname}</td>
                  <td>${item.lastname}</td>
                  <td>${item.email}</td>
                  <td>${item.phone}</td>
                  <td>
                    <a class="btn btn-link btn-edit" role="button" title="Edit contact">
                      <i class="bi bi-pencil-fill"></i>
                    </a>

                    <a class="btn btn-link btn-delete" role="button" title="Delete contact">
                      <i class="bi bi-trash-fill"></i>
                    </a>
                  </td>
                </tr>
              `)

                    $(`#contact-${item.id} .btn-edit`).on('click', function () {
                        $(this).prop('href', `/contact/edit/${item.id}`)
                    })

                    $(`#contact-${item.id} .btn-delete`).on('click', function () {
                        modal(item)
                    })
                })
            } else {
                toast('Error', response.message, 'text-bg-danger')
            }
        },
        complete: function () {
            loading(false);
        }
    });
}

const addContact = (event) => {
    preventSubmit(event)

    if (!validForm()) {
        return
    }

    $.ajax({
        url: '/api/contacts',
        dataType: 'json',
        contentType: 'application/json',
        type: 'POST',
        data: formatData(),
        error: function (jqXHR, textStatus, errorThrown) {
            toast('Error', jqXHR + " " + textStatus + " " + errorThrown, 'text-bg-danger')
        },
        beforeSend: function () {
            loading(true);
        },
        success: function (response) {
            checkResponse(response)
        },
        complete: function () {
            loading(false);
        }
    });
}

const getContact = (id) => {
    if (!id) {
        return
    }

    $.ajax({
        url: `/api/contacts/${id}`,
        dataType: 'json',
        contentType: 'application/json',
        type: 'GET',
        error: function (jqXHR, textStatus, errorThrown) {
            toast('Error', jqXHR + " " + textStatus + " " + errorThrown, 'text-bg-danger')
        },
        beforeSend: function () {
            loading(true);
        },
        success: function (response) {
            if (response.status) {
                const data = response.data

                $('#firstname').val(data.firstname)
                $('#lastname').val(data.lastname)
                $('#email').val(data.email)
                $('#phone').val(data.phone)
            } else {
                toast('Error', response.message, 'text-bg-danger')
            }
        },
        complete: function () {
            loading(false);
        }
    });
}

const updateContact = (event, id) => {
    preventSubmit(event)

    if (!validForm() || !id) {
        return
    }

    $.ajax({
        url: `/api/contacts/${id}`,
        dataType: 'json',
        contentType: 'application/json',
        type: 'PUT',
        data: formatData(),
        error: function (jqXHR, textStatus, errorThrown) {
            toast('Error', jqXHR + " " + textStatus + " " + errorThrown, 'text-bg-danger')
        },
        beforeSend: function () {
            loading(true);
        },
        success: function (response) {
            checkResponse(response)
        },
        complete: function () {
            loading(false);
        }
    });
}

const deleteContact = (id, callback) => {
    if (!id) {
        return
    }

    $.ajax({
        url: `/api/contacts/${id}`,
        dataType: 'json',
        contentType: 'application/json',
        type: 'DELETE',
        error: function (jqXHR, textStatus, errorThrown) {
            toast('Error', jqXHR + " " + textStatus + " " + errorThrown, 'text-bg-danger')
        },
        beforeSend: function () {
            loading(true);
        },
        success: function (response) {
            checkResponse(response, callback)
        },
        complete: function () {
            loading(false);
        }
    });
}
