$(document).ready(function () {
  $(".controller").click(function () {
    var controller = $(this).val();
    $.ajax({
      url: "/access/addactions?role=admin",
      method: "POST",
      data: { controller: controller },
      dataType: "json",
    }).done(function (response) {
      display(response);
    });
  });
});

function display(arr) {
  var html = "";
  for (var i = 0; i < arr.length; i++) {
    html += `
        <option value="${arr[i].replace(".phtml", "")}" name='action'>${arr[
      i
    ].replace(".phtml", "")}</option>
      `;
  }
  $(".action").html(html);
}
