(function () {
  document.addEventListener("submit", function (e) {
    const form = e.submitter.closest("form");
    if (!form) {
      return;
    }

    const element = form.querySelector(".frc-captcha");
    if (!element) {
      return;
    }

    setTimeout(() => {
      if (element.friendlyChallengeWidget) {
        element.friendlyChallengeWidget.reset();
      } else if (element.frcWidget) {
        element.frcWidget.reset();
      }
    }, 1000);
  });
})();