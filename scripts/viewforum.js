function getForumId() {
    const regex = /^.+?\\?id=(?<id>.+)$/
    return regex.exec(window.location.href).groups.id;
}

document.querySelector("#new-topic").addEventListener("click", (e) => {
    window.location.href = `newtopic.php?forum_id=${getForumId()}`;
});