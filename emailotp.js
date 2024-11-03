function sendOTP() {
	const email = document.getElementById('email');
	const otpverify = document.getElementsByClassName('otpverify')[0];

	let otp_val = Math.floor(1000 + Math.random() * 9000);  // Ensuring a 4-digit OTP

	let emailbody = `<h2>Your OTP is </h2><p>${otp_val}</p>`;
	Email.send({
		SecureToken: "06ee5f11-0b74-4750-9b14-90664d8ca7e1",
		To: email.value,
		From: "sscsantiago.isu@gmail.com",
		Subject: "Email OTP using JavaScript",
		Body: emailbody,
	}).then(
		message => {
			if (message === "OK") {
				alert("OTP sent to your email " + email.value);

				otpverify.style.display = "flex";
				const otp_inp = document.getElementById('otp_inp');
				const otp_btn = document.getElementById('otp-btn');

				otp_btn.addEventListener('click', () => {
					if (otp_inp.value == otp_val) {
						alert("Email address verified...");
					} else {
						alert("Invalid OTP");
					}
				});
			} else {
				alert("Failed to send OTP. Please try again.");
			}
		}
	);
}
