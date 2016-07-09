function validateForm() {
  var x = document.forms["mainForm"]["InputUrl"].value;
  if (x == null || x == "") {
    alert("Вы должны заполнить поле с URL адресом!");
    return false;
  }
}