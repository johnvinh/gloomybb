// Client side input validation
document.querySelector('input[name=newtopic]').addEventListener("click", (e) =>
{
    const topicName = document.querySelector("#topic-name").value;
    const topicContent = document.querySelector("#topic-content").value;
    if (topicName === '') {
        e.preventDefault();
        alert("Please input a topic name!");
        return;
    }
    else if (topicContent === "") {
        e.preventDefault();
        alert("Please input content!");
        return;
    }
    e.target.parentElement.submit();
});