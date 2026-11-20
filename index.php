<?php
declare(strict_types=1);

function escapeContactValue(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function contactField(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

$name = '';
$email = '';
$message = '';
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Cache-Control: no-store');

    $name = contactField('name');
    $email = contactField('email');
    $message = contactField('message');

    if (!preg_match('/\A[^\r\n]{1,100}\z/u', $name)) {
        $formError = 'Enter a name of up to 100 characters.';
    } elseif (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formError = 'Enter a valid email address so we can reply.';
    } elseif (!is_string($_POST['message'] ?? '') || !preg_match('/\A.{0,3000}\z/us', $message)) {
        $formError = 'Enter a message of up to 3,000 characters.';
    }

    if ($formError !== '') {
        http_response_code(422);
    } else {
        $recipient = 'contact@findverge.com';
        $body = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}\n";
        $headers = [
            'From' => "FindVerge <{$recipient}>",
            'Reply-To' => $email,
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Transfer-Encoding' => 'base64',
        ];

        if (function_exists('mail') && @mail(
            $recipient,
            'New FindVerge enquiry',
            chunk_split(base64_encode($body)),
            $headers
        )) {
            header('Location: /thank-you/', true, 303);
            exit;
        }

        error_log('FindVerge contact form: the mail transport did not accept the enquiry.');
        http_response_code(503);
        $formError = 'We could not send your enquiry right now. Please try again in a moment.';
    }
}
?>
<!doctype html>
<html lang="en-SG">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="A free guide for job seekers in Singapore: how to search for part-time work-from-home roles, check the details of a role properly, and spot job scams before you share your personal information.">
  <meta name="theme-color" content="#2d3fa6">
  <title>Part-Time Work-from-Home Jobs in Singapore: A Free Guide | FindVerge</title>
  <link rel="icon" href="icon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="sg-style.css">
</head>
<body>
  <a class="skip-link" href="#main">Skip to content</a>

  <div class="notice">
    <div class="wrap">
      <p>An independent guide for job seekers in Singapore. We don't list jobs or recruit.</p>
    </div>
  </div>

  <header class="site-header">
    <div class="wrap header-inner">
      <a class="brand" href="#main">
        <img src="icon.svg" width="34" height="34" alt="">
        FindVerge <small>SG</small>
      </a>
      <nav class="site-nav" aria-label="Main navigation">
        <a href="#options">Options</a>
        <a href="#guide">Guide</a>
        <a href="#red-flags">Scam red flags</a>
        <a href="#tracker">Tracker</a>
        <a href="#faq">FAQ</a>
        <a href="#contact">Contact</a>
      </nav>
    </div>
  </header>

  <main id="main">
    <section class="hero" aria-labelledby="title">
      <div class="wrap hero-grid">
        <div>
          <p class="eyebrow">Free guide for Singapore job seekers</p>
          <h1 id="title">How to find part-time work-from-home jobs in Singapore</h1>
          <p class="lede">Learn how to search for flexible roles, check the details of a role properly, and spot job scams before you share your personal information.</p>
          <div class="hero-actions">
            <a class="button button-accent" href="#guide">Read the guide <span aria-hidden="true">↓</span></a>
            <a class="button button-outline" href="#red-flags">See the scam red flags</a>
          </div>
        </div>

        <figure class="phone">
          <div class="phone-body">
            <div class="phone-screen">
              <div class="phone-head">
                <i aria-hidden="true">?</i>
                <div>Unknown number<small>First message from this contact</small></div>
              </div>
              <p class="bubble">Hi, we have a part-time WFH job. Rate hotels online and receive commission. Add me on Telegram to start.</p>
              <p class="bubble">You need to pay a small deposit to unlock your first task.</p>
              <ul class="flags">
                <li>Contacted you first</li>
                <li>Commission for simple tasks</li>
                <li>Asks you to pay to start</li>
              </ul>
            </div>
          </div>
          <figcaption>A made-up example of a message to be wary of. More red flags are listed below.</figcaption>
        </figure>
      </div>
    </section>

    <section class="wrap section" id="options" aria-labelledby="options-title">
      <div class="section-head">
        <p class="eyebrow">Know the options</p>
        <h2 id="options-title">Flexi-work comes in a few forms</h2>
        <p class="lede">Employers use different terms for similar arrangements. Knowing the difference helps you ask the right questions.</p>
      </div>
      <ul class="cards">
        <li class="card">
          <span class="card-tag">Part-time employee</span>
          <h3>Employed for fewer than 35 hours a week</h3>
          <p>You work under a contract of service. MOM states that part-time employees covered by the Employment Act receive benefits such as leave and paid public holidays, pro-rated by hours worked. <a href="https://www.mom.gov.sg/employment-practices/part-time-employment/who-is-a-part-time-employee">Read MOM's explanation</a>.</p>
          <p class="ask"><strong>Ask:</strong> Will I receive written key employment terms?</p>
        </li>
        <li class="card">
          <span class="card-tag">Freelance or contract</span>
          <h3>Engaged for a project or fixed scope</h3>
          <p>You are usually self-employed and work to an agreement. You may be responsible for your own income tax and some CPF contributions, so check how payment and invoicing work.</p>
          <p class="ask"><strong>Ask:</strong> When and how will I be paid, and what is the scope of work?</p>
        </li>
        <li class="card">
          <span class="card-tag">Hybrid or flexible hours</span>
          <h3>A regular role with some flexibility</h3>
          <p>You might work some days from home, or choose your start and end times within set limits. Arrangements differ a lot between employers.</p>
          <p class="ask"><strong>Ask:</strong> How many days are on-site, and are there core hours?</p>
        </li>
      </ul>
    </section>

    <section class="guide" id="guide" aria-labelledby="guide-title">
      <div class="wrap section split">
        <div class="guide-intro">
          <p class="eyebrow">The guide</p>
          <h2 id="guide-title">Four steps, in a sensible order</h2>
          <p class="lede">Work through these before you send out applications. You can read the whole guide on this page without signing up.</p>
        </div>
        <ol class="timeline">
          <li>
            <h3>Set your hours and check your eligibility</h3>
            <p>Write down how many hours a week you can commit, which days suit you, and whether you are free on weekday mornings, evenings or weekends.</p>
            <p>Some teams work to overseas hours, so check which time zone applies. Singapore uses Singapore Standard Time (SGT, UTC+8).</p>
            <p>If you are not a Singapore Citizen or PR, check what your pass allows with <a href="https://www.mom.gov.sg/passes-and-permits">MOM</a> before you apply.</p>
          </li>
          <li>
            <h3>Search using the words employers use</h3>
            <p>Pair a job title with “work from home”, “WFH”, “hybrid” or “flexi-hours”, then add “part-time” and “Singapore”. Here are some examples.</p>
            <ul class="query-list">
              <li>part-time work from home customer service Singapore</li>
              <li>WFH admin assistant part-time Singapore</li>
              <li>flexi-hours content writer Singapore</li>
              <li>freelance virtual assistant Singapore</li>
            </ul>
            <p>Start with <a href="https://www.mycareersfuture.gov.sg">MyCareersFuture</a> and the careers pages of employers you are interested in. If you spot a role on social media or a messaging app, look for the same role on the employer's own website before you reply.</p>
            <p>Fully remote part-time roles can be limited in Singapore, so keep your search wide. Include hybrid, flexible-hours and freelance work.</p>
          </li>
          <li>
            <h3>Check the role details closely</h3>
            <dl class="check-grid">
              <div><dt>Work arrangement</dt><dd>Is it fully WFH, hybrid or on-site? Is any training done in an office?</dd></div>
              <div><dt>Hours and schedule</dt><dd>How many hours a week? Are the shifts fixed or flexible? Which time zone?</dd></div>
              <div><dt>Employment type</dt><dd>Is it an employee role or freelance work? Ask the employer if it is not clear.</dd></div>
              <div><dt>Pay</dt><dd>Is it stated in S$ per hour, month or project? When and how is it paid?</dd></div>
              <div><dt>Employer</dt><dd>Is the company name clear? Can you find the same job on its own website?</dd></div>
            </dl>
            <p>Note down anything unclear, then ask the employer using the contact details on its own website.</p>
          </li>
          <li>
            <h3>Apply, then keep track</h3>
            <p>Send your CV through the employer's careers page or an email address listed on its website. Keep a record of where you applied so follow-ups are easy.</p>
            <p>If you are hired as an employee for two weeks or longer, MOM requires employers to give you written key employment terms, including pay, hours and working days, within 14 days of your start date. Check that they match what you were told.</p>
          </li>
        </ol>
      </div>
    </section>

    <section class="flags-band" id="red-flags" aria-labelledby="flags-title">
      <div class="wrap section">
        <p class="eyebrow">Scam red flags</p>
        <h2 id="flags-title">Check before you share anything</h2>
        <p class="lede">Job scams are a recurring scam type in Singapore. These are warning signs described by <a href="https://www.scamshield.gov.sg/i-want-protection-from-scams/learn-to-recognise-scams/job-scams/">ScamShield</a> and the Singapore Police Force.</p>

        <ol class="flag-list">
          <li>
            <h3>You are asked to pay first</h3>
            <p>Registration fees, training kits or a “deposit” to start work are red flags. So is using your own money to complete tasks.</p>
          </li>
          <li>
            <h3>Simple tasks for commission</h3>
            <p>ScamShield warns about “jobs” that pay commission for liking posts, rating hotels or apps, or “boosting” product ratings.</p>
          </li>
          <li>
            <h3>Your bank account or PayNow is needed</h3>
            <p>Be wary of any job that asks you to receive and transfer money for someone else, or to use your personal bank account for work.</p>
          </li>
          <li>
            <h3>Singpass is part of the application</h3>
            <p>Never share your Singpass login or scan a Singpass QR code for someone else. Do not change your Singpass mobile number or email to details that another person gives you.</p>
          </li>
          <li>
            <h3>A stranger approaches you first</h3>
            <p>Be careful with unexpected messages on WhatsApp, Telegram, Carousell or SMS, and with invitations to unknown group chats. Check the offer with the employer directly before replying.</p>
          </li>
          <li>
            <h3>A licence number is quoted as proof</h3>
            <p>Scammers have quoted real recruitment agency licence numbers. <a href="https://www.mom.gov.sg/faq/employment-agencies/why-should-i-work-with-a-licensed-ea-and-how-can-i-check-if-they-are-licensed">Check the agency with MOM</a> and contact it using details from its own website.</p>
          </li>
        </ol>

        <div class="protect">
          <h3>Ask why before you send your NRIC</h3>
          <p>Under <a href="https://www.pdpc.gov.sg/nric-faqs">PDPC guidance</a>, private organisations may collect NRIC numbers only where the law requires it, or where it is necessary to verify identity to a high degree of accuracy. Some employers and agencies do have a valid reason later in the process, but do not send a copy of your NRIC in a first message.</p>
        </div>

        <ul class="act" aria-label="ScamShield's Add, Check, Tell approach">
          <li>
            <b>ADD</b>
            <p>Add the ScamShield app to filter scam calls and SMSes. Turn on 2FA and set transaction limits for your internet banking.</p>
          </li>
          <li>
            <b>CHECK</b>
            <p>Check the offer with the employer directly or with someone you trust. If you are unsure, call the ScamShield Helpline on 1799 (24 hours).</p>
          </li>
          <li>
            <b>TELL</b>
            <p>Warn family and friends. Report and block suspicious accounts and chat groups.</p>
          </li>
        </ul>
      </div>
    </section>

    <section class="wrap section split split-even split-middle" id="tracker" aria-labelledby="tracker-title">
      <div class="tracker-copy">
        <p class="eyebrow">Free template</p>
        <h2 id="tracker-title">Keep your search organised</h2>
        <p class="lede">Record the employer, role link, work arrangement, hours, pay and your next step in one place. Open the CSV file in Excel, Google Sheets or Numbers, or copy the column names into your own notes.</p>
        <a class="button button-primary" href="wfh-job-search-tracker.csv" download>Download the tracker <span aria-hidden="true">↓</span></a>
      </div>
      <div>
        <div class="tracker-card">
          <header><span>Preview of the template</span><span>CSV</span></header>
          <div class="table-scroll">
            <table>
              <caption class="visually-hidden">Preview of the tracker columns</caption>
              <thead>
                <tr><th scope="col">Employer</th><th scope="col">Role</th><th scope="col">Arrangement</th><th scope="col">Hours (SGT)</th><th scope="col">Pay (S$)</th><th scope="col">Status</th></tr>
              </thead>
              <tbody>
                <tr><td>Company name</td><td>Job title</td><td>WFH, hybrid or on-site</td><td>Hours per week</td><td>Per hour or month</td><td>Applied, interview, reply</td></tr>
              </tbody>
            </table>
          </div>
        </div>
        <p class="small-note">The full file also has columns for the role link, whether you found the role on the employer's own website, the date applied and your next step.</p>
      </div>
    </section>

    <section class="faq" id="faq" aria-labelledby="faq-title">
      <div class="wrap section split">
        <div>
          <p class="eyebrow">FAQ</p>
          <h2 id="faq-title">Common questions</h2>
          <p class="lede">Quick answers to questions we are often asked.</p>
        </div>
        <div class="faq-list">
          <details>
            <summary>Is FindVerge a job board or recruitment agency?</summary>
            <p>No. We publish educational guides only. We do not list jobs, recruit, or place candidates, and we are not affiliated with any government agency, employer or recruiter.</p>
          </details>
          <details>
            <summary>Are part-time work-from-home jobs easy to find in Singapore?</summary>
            <p>It varies. Many part-time roles in Singapore are on-site, such as in retail and food and beverage, and fully remote roles can be limited. Widening your search to hybrid, flexible-hours and freelance work gives you more options to compare.</p>
          </details>
          <details>
            <summary>Can I work part-time if I am a student or hold a work pass?</summary>
            <p>It depends on your pass and your school's rules. Check the conditions with <a href="https://www.mom.gov.sg/passes-and-permits">MOM</a> and your institution before you apply.</p>
          </details>
          <details>
            <summary>Can I take on a part-time job on top of my current one?</summary>
            <p>Often yes for Singapore Citizens and PRs, but check your employment contract first. Some employers require you to declare outside work or get approval. Public service employees generally need approval, and work pass holders generally cannot take a second job. <a href="https://www.mom.gov.sg/employment-practices/part-time-employment">Read MOM's guide to part-time employment</a>.</p>
          </details>
          <details>
            <summary>Is it normal to be asked to pay before starting a job?</summary>
            <p>ScamShield lists paying upfront and using your own money to complete tasks as signs of a likely scam. If you are unsure about an offer, call the ScamShield Helpline on 1799.</p>
          </details>
          <details>
            <summary>I already have a job. Can I ask for flexible hours?</summary>
            <p>Under the Tripartite Guidelines on Flexible Work Arrangement Requests, which took effect on 1 December 2024, employees who have completed their probation can make a formal written request, and employers should respond within two months. Employers make the final decision. <a href="https://www.mom.gov.sg/employment-practices/good-work-practices/flexible-work-arrangements">Read MOM's guidelines</a>.</p>
          </details>
          <details>
            <summary>How do you use my details?</summary>
            <p>We use your name and email address only to reply to your enquiry. Read our <a href="privacy/">privacy policy</a> for details.</p>
          </details>
        </div>
      </div>
    </section>

    <section class="contact" id="contact" aria-labelledby="contact-title">
      <div class="wrap section split split-even">
        <div>
          <p class="eyebrow">Contact</p>
          <h2 id="contact-title">Send us an enquiry</h2>
          <p class="lede">Have a question about the guide, or a topic you would like us to cover? Leave your name and email address and we will reply by email. We cannot help with job applications or placements.</p>
        </div>
        <form class="contact-form" method="post" action="./#contact">
          <?php if ($formError !== ''): ?>
            <p class="form-error" role="alert"><?= escapeContactValue($formError) ?></p>
          <?php endif; ?>
          <fieldset>
            <div class="field">
              <label for="contact-name">Name</label>
              <input id="contact-name" name="name" autocomplete="name" maxlength="100" value="<?= escapeContactValue($name) ?>" required>
            </div>
            <div class="field">
              <label for="contact-email">Email address</label>
              <input id="contact-email" name="email" type="email" autocomplete="email" maxlength="254" value="<?= escapeContactValue($email) ?>" required>
            </div>
            <div class="field">
              <label for="contact-message">Message <span>(optional)</span></label>
              <textarea id="contact-message" name="message" rows="5" maxlength="3000"><?= escapeContactValue($message) ?></textarea>
            </div>
            <p class="form-note">We use your details only to reply to you. Please do not include your NRIC number, bank details or passwords. See our <a href="privacy/">privacy policy</a>.</p>
            <button class="button button-primary" type="submit">Send enquiry</button>
          </fieldset>
        </form>
      </div>
    </section>
  </main>

  <footer class="site-footer" id="about">
    <div class="wrap footer-inner">
      <div>
        <a class="brand" href="#main">
          <img src="icon.svg" width="34" height="34" alt="">
          FindVerge <small>SG</small>
        </a>
        <p class="disclaimer">FindVerge is an independent educational resource for job seekers in Singapore. We do not list jobs, recruit or place candidates, and we are not affiliated with any government agency, employer or recruiter. The information here is general guidance, last checked in November 2026, and may change. Please refer to the official sources linked on this page.</p>
      </div>
      <div class="footer-bottom">
        <p>© 2026 FindVerge. All rights reserved.</p>
        <div class="footer-links">
          <a href="privacy/">Privacy policy</a>
          <a href="#main">Back to top <span aria-hidden="true">↑</span></a>
        </div>
      </div>
    </div>
  </footer>
</body>
</html>
