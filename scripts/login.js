function addErrorAndCancel(e)
{
    const errorSpace = document.querySelector('main > div:first-child');
    const username = document.querySelector('#username').value;
    const password = document.querySelector('#password').value;
    // Error stuff
    const currentError = document.querySelector('.error');
    if (currentError !== null)
        currentError.parentElement.removeChild(currentError);
    const error = document.createElement('p');
    error.classList.add('error');
    if (username === '') {
        e.preventDefault();
        error.innerHTML = 'Please enter a username.';
        errorSpace.parentElement.insertBefore(error, errorSpace.nextSibling);
        return;
    } else if (password === '') {
        e.preventDefault();
        error.innerHTML = 'Please enter a password.';
        errorSpace.parentElement.insertBefore(error, errorSpace.nextSibling);
        return;
    }
    e.target.parentElement.submit();
}

const login = document.querySelector('input[name=login]');
const signup = document.querySelector('input[name=signup]');

if (login !== null) {
    login.addEventListener('click', (e) =>
    {
        addErrorAndCancel(e);
    });
}
else if (signup !== null) {
    signup.addEventListener('click', (e) =>
    {
        addErrorAndCancel(e);
    });
}