/*!
 * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
 * Copyright 2011-2024 The Bootstrap Authors
 * Licensed under the Creative Commons Attribution 3.0 Unported License.
 */

(() => {
	'use strict'
  
	const getStoredTheme = () => localStorage.getItem('theme')
	const setStoredTheme = theme => localStorage.setItem('theme', theme)
  
	const getPreferredTheme = () => {
	  const storedTheme = getStoredTheme()
	  if (storedTheme) {
		return storedTheme
	  }
  
	  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
	}
  
	const setTheme = theme => {
	  if (theme === 'auto') {
		document.documentElement.setAttribute('data-bs-theme', (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'))
	  } else {
		document.documentElement.setAttribute('data-bs-theme', theme)
	  }
	}
  
	setTheme(getPreferredTheme())
  
	const showActiveTheme = (theme, focus = false) => {
	  const themeSwitcher = document.querySelector('#bd-theme')
  
	  if (!themeSwitcher) {
		return
	  }
  
	  const themeSwitcherText = document.querySelector('#bd-theme-text')
	  const activeThemeIcon = document.querySelector('.theme-icon-active use')
	  const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
	  const svgOfActiveBtn = btnToActive.querySelector('svg use').getAttribute('href')
  
	  document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
		element.classList.remove('active')
		element.setAttribute('aria-pressed', 'false')
	  })
  
	  btnToActive.classList.add('active')
	  btnToActive.setAttribute('aria-pressed', 'true')
	  activeThemeIcon.setAttribute('href', svgOfActiveBtn)
	  const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`
	  themeSwitcher.setAttribute('aria-label', themeSwitcherLabel)
  
	  if (focus) {
		themeSwitcher.focus()
	  }
	}
  
	window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
	  const storedTheme = getStoredTheme()
	  if (storedTheme !== 'light' && storedTheme !== 'dark') {
		setTheme(getPreferredTheme())
	  }
	})
  
	window.addEventListener('DOMContentLoaded', () => {
	  showActiveTheme(getPreferredTheme())
  
	  document.querySelectorAll('[data-bs-theme-value]')
		.forEach(toggle => {
		  toggle.addEventListener('click', () => {
			const theme = toggle.getAttribute('data-bs-theme-value')
			setStoredTheme(theme)
			setTheme(theme)
			showActiveTheme(theme, true)
		  })
		})
	})
  })()

const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    }
});

function controlTag(e){
	tecla = (document.all) ? e.keyCode : e.which;
	if(tecla == 8) return true;

	else if(tecla == 0 || tecla == 9) return true;
	patron = /[0-9\s]/;
	n = String.fromCharCode(tecla);
	return patron.test(n);
}

function testText(txtString){
	var stringText = new RegExp(/^[a-zA-ZÑñÁáÉéÍíÓóÚúÜü\s]+$/);
	if(stringText.test(txtString)){
		return true;
	}else{
		return false;
	}
}

function testEntero(intCant){
	var intCantidad = new RegExp(/^([0-9])*$/);
	if(intCantidad.test(intCant)){
		return true;
	}else{
		return false;
	}
}

function fntEmailValidate(email){
	var stringEmail = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9])+\.)+([a-zA-Z0-9]{2,4})+$/);
	if(stringEmail.test(email) == false){
		return false;
	}else{
		return true;
	}
}

function fntValidText(){
	let validText = document.querySelectorAll(".validText");
	validText.forEach(function(validText){
		validText.addEventListener('keyup', function(){
			let inputValue = this.value;
			if(!testText(inputValue)){
				this.classList.add('is-invalid');
			}else{
				this.classList.remove('is-invalid');
			}
		});
	});
}

function fntValidNumber(){
	let validNumber = document.querySelectorAll(".validNumber");
	validNumber.forEach(function(validNumber){
		validNumber.addEventListener('keyup', function(){
			let inputValue = this.value;
			if(!testEntero(inputValue)){
				this.classList.add('is-invalid');
			}else{
				this.classList.remove('is-invalid');
			}
		});
	});
}

function fntValidEmail(){
	let validEmail = document.querySelectorAll(".validEmail");
	validEmail.forEach(function(validEmail){
		validEmail.addEventListener('keyup', function(){
			let inputValue = this.value;
			if(!fntEmailValidate(inputValue)){
				this.classList.add('is-invalid');
			}else{
				this.classList.remove('is-invalid');
			}
		});
	});
}

window.addEventListener('load', function(){
	fntValidText();
	fntValidNumber();
	fntValidEmail();
}, false);