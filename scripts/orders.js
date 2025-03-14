
const clientInput =  document.querySelector('#client');

const emailField =  document.querySelector('#email-field');

const toggleVisibleemailField = () => {
    const value = clientInput.value;
    if(value === 'new'){
        emailField.style.display = 'block';

    }else{
        emailField.style.display = 'none';
    }

}

toggleVisibleemailField();
clientInput.addEventListener('input',toggleVisibleemailField);


