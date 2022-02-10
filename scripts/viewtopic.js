function getTopicId()
{
    const regex = /^.+?\\?id=(?<id>.+)$/
    return regex.exec(window.location.href).groups.id;
}

document.querySelector('#new-reply').addEventListener('click', () =>
{
    window.location.href = `newreply.php?id=${getTopicId()}`;
});