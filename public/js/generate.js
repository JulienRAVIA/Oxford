function randomCode(length, chars = 'default') {
	if(chars == 'default') {
		chars = "0123456789";
	}
    var pass = "";
    for (var x = 0; x < length; x++) {
        var i = Math.floor(Math.random() * chars.length);
        pass += chars.charAt(i);
    }
    return pass;
}

function randomPassword() {
  numLc = 5;
  numDigits = 2;
  numSpecial = 1;

  var lcLetters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  var numbers = '0123456789';
  var special = '$@$!%*#?&';

  var getRand = function(values) {
    return values.charAt(Math.floor(Math.random() * values.length));
  }

  //+ Jonas Raoni Soares Silva
  //@ http://jsfromhell.com/array/shuffle [v1.0]
  function shuffle(o){ //v1.0
    for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
    return o;
  };

  var pass = [];
  for(var i = 0; i < numLc; ++i) { pass.push(getRand(lcLetters)) }
  for(var i = 0; i < numDigits; ++i) { pass.push(getRand(numbers)) }
  for(var i = 0; i < numSpecial; ++i) { pass.push(getRand(special)) }

  return shuffle(pass).join('');
}

function generatePassword() {
    jobForm.password.value = randomPassword(8);
}

function generateAccessCode() {
    codeForm.code.value = randomCode(4, '0123456789');
}