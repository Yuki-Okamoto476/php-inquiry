//カードの「詳細を見る」ボタンを押した時の挙動
const detail_button = document.querySelectorAll(".contact_list__button");
const detail_info = document.querySelectorAll(".contact_list__detail-info");
for (let i = 0; i < detail_button.length; i++) {
  detail_button[i].addEventListener("click", () => {
    detail_info[i].classList.toggle("detail-display");
    if (detail_button[i].innerHTML === "詳細を見る") {
      detail_button[i].innerHTML = "閉じる";
    } else {
      detail_button[i].innerHTML = "詳細を見る";
    }
  });
}
