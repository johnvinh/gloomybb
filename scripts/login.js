document.querySelector('input[name=login]').addEventListener('click', (e) =>
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
});