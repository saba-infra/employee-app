document.getElementById("searchForm").addEventListener("submit", function(event) {
    event.preventDefault();
    const inputId = document.getElementById("employee_id").value;

    fetch("search.php?employee_id=" + encodeURIComponent(inputId))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("result").textContent = "検索結果：" + data.name;
            } else {
                const errorMsg = data.message || "該当する社員が見つかりませんでした。";
                document.getElementById("result").textContent = errorMsg;
            }
        })
        .catch(error => {
            console.error("通信エラー:", error);
            document.getElementById("result").textContent = "検索中に通信エラーが発生しました。";
        });
});