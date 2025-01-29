"use strict";
(self["webpackChunkmangaquest"] = self["webpackChunkmangaquest"] || []).push([["assets_controllers_csrf_protection_controller_js"],{

/***/ "./assets/controllers/csrf_protection_controller.js":
/*!**********************************************************!*\
  !*** ./assets/controllers/csrf_protection_controller.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
var nameCheck = /^[-_a-zA-Z0-9]{4,22}$/;
var tokenCheck = /^[-_/+a-zA-Z0-9]{24,}$/;

// Generate and double-submit a CSRF token in a form field and a cookie, as defined by Symfony's SameOriginCsrfTokenManager
document.addEventListener('submit', function (event) {
  var csrfField = event.target.querySelector('input[data-controller="csrf-protection"]');
  if (!csrfField) {
    return;
  }
  var csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');
  var csrfToken = csrfField.value;
  if (!csrfCookie && nameCheck.test(csrfToken)) {
    csrfField.setAttribute('data-csrf-protection-cookie-value', csrfCookie = csrfToken);
    csrfField.value = csrfToken = btoa(String.fromCharCode.apply(null, (window.crypto || window.msCrypto).getRandomValues(new Uint8Array(18))));
  }
  if (csrfCookie && tokenCheck.test(csrfToken)) {
    var cookie = csrfCookie + '_' + csrfToken + '=' + csrfCookie + '; path=/; samesite=strict';
    document.cookie = window.location.protocol === 'https:' ? '__Host-' + cookie + '; secure' : cookie;
  }
});

// When @hotwired/turbo handles form submissions, send the CSRF token in a header in addition to a cookie
// The `framework.csrf_protection.check_header` config option needs to be enabled for the header to be checked
document.addEventListener('turbo:submit-start', function (event) {
  var csrfField = event.detail.formSubmission.formElement.querySelector('input[data-controller="csrf-protection"]');
  if (!csrfField) {
    return;
  }
  var csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');
  if (tokenCheck.test(csrfField.value) && nameCheck.test(csrfCookie)) {
    event.detail.formSubmission.fetchRequest.headers[csrfCookie] = csrfField.value;
  }
});

// When @hotwired/turbo handles form submissions, remove the CSRF cookie once a form has been submitted
document.addEventListener('turbo:submit-end', function (event) {
  var csrfField = event.detail.formSubmission.formElement.querySelector('input[data-controller="csrf-protection"]');
  if (!csrfField) {
    return;
  }
  var csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');
  if (tokenCheck.test(csrfField.value) && nameCheck.test(csrfCookie)) {
    var cookie = csrfCookie + '_' + csrfField.value + '=0; path=/; samesite=strict; max-age=0';
    document.cookie = window.location.protocol === 'https:' ? '__Host-' + cookie + '; secure' : cookie;
  }
});

/* stimulusFetch: 'lazy' */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ('csrf-protection-controller');

/***/ })

}]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXNzZXRzX2NvbnRyb2xsZXJzX2NzcmZfcHJvdGVjdGlvbl9jb250cm9sbGVyX2pzLmpzIiwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7QUFBQSxJQUFJQSxTQUFTLEdBQUcsdUJBQXVCO0FBQ3ZDLElBQUlDLFVBQVUsR0FBRyx3QkFBd0I7O0FBRXpDO0FBQ0FDLFFBQVEsQ0FBQ0MsZ0JBQWdCLENBQUMsUUFBUSxFQUFFLFVBQVVDLEtBQUssRUFBRTtFQUNqRCxJQUFJQyxTQUFTLEdBQUdELEtBQUssQ0FBQ0UsTUFBTSxDQUFDQyxhQUFhLENBQUMsMENBQTBDLENBQUM7RUFFdEYsSUFBSSxDQUFDRixTQUFTLEVBQUU7SUFDWjtFQUNKO0VBRUEsSUFBSUcsVUFBVSxHQUFHSCxTQUFTLENBQUNJLFlBQVksQ0FBQyxtQ0FBbUMsQ0FBQztFQUM1RSxJQUFJQyxTQUFTLEdBQUdMLFNBQVMsQ0FBQ00sS0FBSztFQUUvQixJQUFJLENBQUNILFVBQVUsSUFBSVIsU0FBUyxDQUFDWSxJQUFJLENBQUNGLFNBQVMsQ0FBQyxFQUFFO0lBQzFDTCxTQUFTLENBQUNRLFlBQVksQ0FBQyxtQ0FBbUMsRUFBRUwsVUFBVSxHQUFHRSxTQUFTLENBQUM7SUFDbkZMLFNBQVMsQ0FBQ00sS0FBSyxHQUFHRCxTQUFTLEdBQUdJLElBQUksQ0FBQ0MsTUFBTSxDQUFDQyxZQUFZLENBQUNDLEtBQUssQ0FBQyxJQUFJLEVBQUUsQ0FBQ0MsTUFBTSxDQUFDQyxNQUFNLElBQUlELE1BQU0sQ0FBQ0UsUUFBUSxFQUFFQyxlQUFlLENBQUMsSUFBSUMsVUFBVSxDQUFDLEVBQUUsQ0FBQyxDQUFDLENBQUMsQ0FBQztFQUMvSTtFQUVBLElBQUlkLFVBQVUsSUFBSVAsVUFBVSxDQUFDVyxJQUFJLENBQUNGLFNBQVMsQ0FBQyxFQUFFO0lBQzFDLElBQUlhLE1BQU0sR0FBR2YsVUFBVSxHQUFHLEdBQUcsR0FBR0UsU0FBUyxHQUFHLEdBQUcsR0FBR0YsVUFBVSxHQUFHLDJCQUEyQjtJQUMxRk4sUUFBUSxDQUFDcUIsTUFBTSxHQUFHTCxNQUFNLENBQUNNLFFBQVEsQ0FBQ0MsUUFBUSxLQUFLLFFBQVEsR0FBRyxTQUFTLEdBQUdGLE1BQU0sR0FBRyxVQUFVLEdBQUdBLE1BQU07RUFDdEc7QUFDSixDQUFDLENBQUM7O0FBRUY7QUFDQTtBQUNBckIsUUFBUSxDQUFDQyxnQkFBZ0IsQ0FBQyxvQkFBb0IsRUFBRSxVQUFVQyxLQUFLLEVBQUU7RUFDN0QsSUFBSUMsU0FBUyxHQUFHRCxLQUFLLENBQUNzQixNQUFNLENBQUNDLGNBQWMsQ0FBQ0MsV0FBVyxDQUFDckIsYUFBYSxDQUFDLDBDQUEwQyxDQUFDO0VBRWpILElBQUksQ0FBQ0YsU0FBUyxFQUFFO0lBQ1o7RUFDSjtFQUVBLElBQUlHLFVBQVUsR0FBR0gsU0FBUyxDQUFDSSxZQUFZLENBQUMsbUNBQW1DLENBQUM7RUFFNUUsSUFBSVIsVUFBVSxDQUFDVyxJQUFJLENBQUNQLFNBQVMsQ0FBQ00sS0FBSyxDQUFDLElBQUlYLFNBQVMsQ0FBQ1ksSUFBSSxDQUFDSixVQUFVLENBQUMsRUFBRTtJQUNoRUosS0FBSyxDQUFDc0IsTUFBTSxDQUFDQyxjQUFjLENBQUNFLFlBQVksQ0FBQ0MsT0FBTyxDQUFDdEIsVUFBVSxDQUFDLEdBQUdILFNBQVMsQ0FBQ00sS0FBSztFQUNsRjtBQUNKLENBQUMsQ0FBQzs7QUFFRjtBQUNBVCxRQUFRLENBQUNDLGdCQUFnQixDQUFDLGtCQUFrQixFQUFFLFVBQVVDLEtBQUssRUFBRTtFQUMzRCxJQUFJQyxTQUFTLEdBQUdELEtBQUssQ0FBQ3NCLE1BQU0sQ0FBQ0MsY0FBYyxDQUFDQyxXQUFXLENBQUNyQixhQUFhLENBQUMsMENBQTBDLENBQUM7RUFFakgsSUFBSSxDQUFDRixTQUFTLEVBQUU7SUFDWjtFQUNKO0VBRUEsSUFBSUcsVUFBVSxHQUFHSCxTQUFTLENBQUNJLFlBQVksQ0FBQyxtQ0FBbUMsQ0FBQztFQUU1RSxJQUFJUixVQUFVLENBQUNXLElBQUksQ0FBQ1AsU0FBUyxDQUFDTSxLQUFLLENBQUMsSUFBSVgsU0FBUyxDQUFDWSxJQUFJLENBQUNKLFVBQVUsQ0FBQyxFQUFFO0lBQ2hFLElBQUllLE1BQU0sR0FBR2YsVUFBVSxHQUFHLEdBQUcsR0FBR0gsU0FBUyxDQUFDTSxLQUFLLEdBQUcsd0NBQXdDO0lBRTFGVCxRQUFRLENBQUNxQixNQUFNLEdBQUdMLE1BQU0sQ0FBQ00sUUFBUSxDQUFDQyxRQUFRLEtBQUssUUFBUSxHQUFHLFNBQVMsR0FBR0YsTUFBTSxHQUFHLFVBQVUsR0FBR0EsTUFBTTtFQUN0RztBQUNKLENBQUMsQ0FBQzs7QUFFRjtBQUNBLGlFQUFlLDRCQUE0QiIsInNvdXJjZXMiOlsid2VicGFjazovL21hbmdhcXVlc3QvLi9hc3NldHMvY29udHJvbGxlcnMvY3NyZl9wcm90ZWN0aW9uX2NvbnRyb2xsZXIuanMiXSwic291cmNlc0NvbnRlbnQiOlsidmFyIG5hbWVDaGVjayA9IC9eWy1fYS16QS1aMC05XXs0LDIyfSQvO1xudmFyIHRva2VuQ2hlY2sgPSAvXlstXy8rYS16QS1aMC05XXsyNCx9JC87XG5cbi8vIEdlbmVyYXRlIGFuZCBkb3VibGUtc3VibWl0IGEgQ1NSRiB0b2tlbiBpbiBhIGZvcm0gZmllbGQgYW5kIGEgY29va2llLCBhcyBkZWZpbmVkIGJ5IFN5bWZvbnkncyBTYW1lT3JpZ2luQ3NyZlRva2VuTWFuYWdlclxuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignc3VibWl0JywgZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgdmFyIGNzcmZGaWVsZCA9IGV2ZW50LnRhcmdldC5xdWVyeVNlbGVjdG9yKCdpbnB1dFtkYXRhLWNvbnRyb2xsZXI9XCJjc3JmLXByb3RlY3Rpb25cIl0nKTtcblxuICAgIGlmICghY3NyZkZpZWxkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB2YXIgY3NyZkNvb2tpZSA9IGNzcmZGaWVsZC5nZXRBdHRyaWJ1dGUoJ2RhdGEtY3NyZi1wcm90ZWN0aW9uLWNvb2tpZS12YWx1ZScpO1xuICAgIHZhciBjc3JmVG9rZW4gPSBjc3JmRmllbGQudmFsdWU7XG5cbiAgICBpZiAoIWNzcmZDb29raWUgJiYgbmFtZUNoZWNrLnRlc3QoY3NyZlRva2VuKSkge1xuICAgICAgICBjc3JmRmllbGQuc2V0QXR0cmlidXRlKCdkYXRhLWNzcmYtcHJvdGVjdGlvbi1jb29raWUtdmFsdWUnLCBjc3JmQ29va2llID0gY3NyZlRva2VuKTtcbiAgICAgICAgY3NyZkZpZWxkLnZhbHVlID0gY3NyZlRva2VuID0gYnRvYShTdHJpbmcuZnJvbUNoYXJDb2RlLmFwcGx5KG51bGwsICh3aW5kb3cuY3J5cHRvIHx8IHdpbmRvdy5tc0NyeXB0bykuZ2V0UmFuZG9tVmFsdWVzKG5ldyBVaW50OEFycmF5KDE4KSkpKTtcbiAgICB9XG5cbiAgICBpZiAoY3NyZkNvb2tpZSAmJiB0b2tlbkNoZWNrLnRlc3QoY3NyZlRva2VuKSkge1xuICAgICAgICB2YXIgY29va2llID0gY3NyZkNvb2tpZSArICdfJyArIGNzcmZUb2tlbiArICc9JyArIGNzcmZDb29raWUgKyAnOyBwYXRoPS87IHNhbWVzaXRlPXN0cmljdCc7XG4gICAgICAgIGRvY3VtZW50LmNvb2tpZSA9IHdpbmRvdy5sb2NhdGlvbi5wcm90b2NvbCA9PT0gJ2h0dHBzOicgPyAnX19Ib3N0LScgKyBjb29raWUgKyAnOyBzZWN1cmUnIDogY29va2llO1xuICAgIH1cbn0pO1xuXG4vLyBXaGVuIEBob3R3aXJlZC90dXJibyBoYW5kbGVzIGZvcm0gc3VibWlzc2lvbnMsIHNlbmQgdGhlIENTUkYgdG9rZW4gaW4gYSBoZWFkZXIgaW4gYWRkaXRpb24gdG8gYSBjb29raWVcbi8vIFRoZSBgZnJhbWV3b3JrLmNzcmZfcHJvdGVjdGlvbi5jaGVja19oZWFkZXJgIGNvbmZpZyBvcHRpb24gbmVlZHMgdG8gYmUgZW5hYmxlZCBmb3IgdGhlIGhlYWRlciB0byBiZSBjaGVja2VkXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCd0dXJibzpzdWJtaXQtc3RhcnQnLCBmdW5jdGlvbiAoZXZlbnQpIHtcbiAgICB2YXIgY3NyZkZpZWxkID0gZXZlbnQuZGV0YWlsLmZvcm1TdWJtaXNzaW9uLmZvcm1FbGVtZW50LnF1ZXJ5U2VsZWN0b3IoJ2lucHV0W2RhdGEtY29udHJvbGxlcj1cImNzcmYtcHJvdGVjdGlvblwiXScpO1xuXG4gICAgaWYgKCFjc3JmRmllbGQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHZhciBjc3JmQ29va2llID0gY3NyZkZpZWxkLmdldEF0dHJpYnV0ZSgnZGF0YS1jc3JmLXByb3RlY3Rpb24tY29va2llLXZhbHVlJyk7XG5cbiAgICBpZiAodG9rZW5DaGVjay50ZXN0KGNzcmZGaWVsZC52YWx1ZSkgJiYgbmFtZUNoZWNrLnRlc3QoY3NyZkNvb2tpZSkpIHtcbiAgICAgICAgZXZlbnQuZGV0YWlsLmZvcm1TdWJtaXNzaW9uLmZldGNoUmVxdWVzdC5oZWFkZXJzW2NzcmZDb29raWVdID0gY3NyZkZpZWxkLnZhbHVlO1xuICAgIH1cbn0pO1xuXG4vLyBXaGVuIEBob3R3aXJlZC90dXJibyBoYW5kbGVzIGZvcm0gc3VibWlzc2lvbnMsIHJlbW92ZSB0aGUgQ1NSRiBjb29raWUgb25jZSBhIGZvcm0gaGFzIGJlZW4gc3VibWl0dGVkXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCd0dXJibzpzdWJtaXQtZW5kJywgZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgdmFyIGNzcmZGaWVsZCA9IGV2ZW50LmRldGFpbC5mb3JtU3VibWlzc2lvbi5mb3JtRWxlbWVudC5xdWVyeVNlbGVjdG9yKCdpbnB1dFtkYXRhLWNvbnRyb2xsZXI9XCJjc3JmLXByb3RlY3Rpb25cIl0nKTtcblxuICAgIGlmICghY3NyZkZpZWxkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB2YXIgY3NyZkNvb2tpZSA9IGNzcmZGaWVsZC5nZXRBdHRyaWJ1dGUoJ2RhdGEtY3NyZi1wcm90ZWN0aW9uLWNvb2tpZS12YWx1ZScpO1xuXG4gICAgaWYgKHRva2VuQ2hlY2sudGVzdChjc3JmRmllbGQudmFsdWUpICYmIG5hbWVDaGVjay50ZXN0KGNzcmZDb29raWUpKSB7XG4gICAgICAgIHZhciBjb29raWUgPSBjc3JmQ29va2llICsgJ18nICsgY3NyZkZpZWxkLnZhbHVlICsgJz0wOyBwYXRoPS87IHNhbWVzaXRlPXN0cmljdDsgbWF4LWFnZT0wJztcblxuICAgICAgICBkb2N1bWVudC5jb29raWUgPSB3aW5kb3cubG9jYXRpb24ucHJvdG9jb2wgPT09ICdodHRwczonID8gJ19fSG9zdC0nICsgY29va2llICsgJzsgc2VjdXJlJyA6IGNvb2tpZTtcbiAgICB9XG59KTtcblxuLyogc3RpbXVsdXNGZXRjaDogJ2xhenknICovXG5leHBvcnQgZGVmYXVsdCAnY3NyZi1wcm90ZWN0aW9uLWNvbnRyb2xsZXInO1xuIl0sIm5hbWVzIjpbIm5hbWVDaGVjayIsInRva2VuQ2hlY2siLCJkb2N1bWVudCIsImFkZEV2ZW50TGlzdGVuZXIiLCJldmVudCIsImNzcmZGaWVsZCIsInRhcmdldCIsInF1ZXJ5U2VsZWN0b3IiLCJjc3JmQ29va2llIiwiZ2V0QXR0cmlidXRlIiwiY3NyZlRva2VuIiwidmFsdWUiLCJ0ZXN0Iiwic2V0QXR0cmlidXRlIiwiYnRvYSIsIlN0cmluZyIsImZyb21DaGFyQ29kZSIsImFwcGx5Iiwid2luZG93IiwiY3J5cHRvIiwibXNDcnlwdG8iLCJnZXRSYW5kb21WYWx1ZXMiLCJVaW50OEFycmF5IiwiY29va2llIiwibG9jYXRpb24iLCJwcm90b2NvbCIsImRldGFpbCIsImZvcm1TdWJtaXNzaW9uIiwiZm9ybUVsZW1lbnQiLCJmZXRjaFJlcXVlc3QiLCJoZWFkZXJzIl0sInNvdXJjZVJvb3QiOiIifQ==