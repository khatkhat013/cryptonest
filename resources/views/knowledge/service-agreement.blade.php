@extends('layouts.app')

@section('content')
<x-page-header 
    :back-url="url('/')"
    title="Service Agreement"
/>

<div class="container">
    <!-- Important Hint Section -->
    <div class="card shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body">
            <h5 class="text-danger mb-3">Important Hint:</h5>
            <p class="fw-medium mb-3">We hereby specially remind you:</p>
            <ol class="mb-0">
                <li class="mb-2">The digital assets themselves are not issued by any financial institution or company or this website;</li>
                <li class="mb-2">The digital asset market is new, unconfirmed and may not grow;</li>
                <li class="mb-2">Digital assets are mainly used extensively by speculators, with relatively little use in retail and commercial markets. Digital asset transactions are extremely risky. They are traded continuously throughout the day, with no limit on rise and fall, and prices are easily affected by market makers and global government policies. And large fluctuations;</li>
                <li>If the Company determines, in its sole discretion, that you have violated this Agreement, or that the services provided by this website or your use of the services provided by this website are illegal under the laws of your jurisdiction, the Company has the right to suspend or terminate you at any time. account, or suspend or terminate your use of the services or digital asset transactions provided by this website.</li>
            </ol>
        </div>
    </div>

    <!-- Please Note Section -->
    <div class="card shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body">
            <h5 class="mb-3">Please Note:</h5>
            <ol class="mb-0">
                <li class="mb-2">Any opinions, news, discussions, analyses, prices, recommendations and other information on this website are general market comments and do not constitute investment advice. We are not responsible for any losses arising directly or indirectly from reliance on this information, including but not limited to any loss of profits.</li>
                <li class="mb-2">The content of this website is subject to change at any time without prior notice. We have taken reasonable measures to ensure the accuracy of the information on the website, but we cannot guarantee its accuracy, and we will not be responsible for any loss or failure due to the information on this website.</li>
                <li class="mb-2">There are also risks in using Internet-based trading systems, including but not limited to failure of software, hardware and Internet links. As we have no control over the reliability and availability of the Internet, we accept no responsibility for distortion, delays and link failures.</li>
                <li>It is prohibited to use this website to conduct malicious market manipulation, unfair transactions and other unethical trading activities. If such incidents are discovered, this website will warn and restrict transactions for all unethical behaviors such as malicious price manipulation and malicious influence on the trading system., account closure and other preventive protection measures, we do not assume all responsibilities arising therefrom and reserve the right to pursue liability from relevant persons.</li>
            </ol>
        </div>
    </div>

    <!-- Agreement Sections -->
    <div class="card shadow-sm" style="border-radius: 15px;">
        <div class="card-body">
            <div class="mb-4">
                <h5 class="mb-3">1. General principles</h5>
                <div class="ps-3">
                    <p class="mb-3">1.1 "User Agreement" (hereinafter referred to as "this Agreement" or "These Terms and Conditions"), consisting of the main text, "Privacy Terms", "Know Your Customer and Anti-Money Laundering Policy" and other information that has been published or may be published in the future on this website It consists of various rules, statements, instructions, etc.</p>
                    <p class="mb-3">1.2 You should read this agreement carefully before using the services provided by this website. If you do not understand anything or if it is otherwise necessary, please consult a professional lawyer. If you do not agree to this Agreement and/or its modification at any time, please immediately stop using the services provided by this website or no longer log in to this website. Once you log in to this website, use any services of this website or any other similar behavior, it means that you have understood and fully agreed to the contents of this Agreement, including any modifications made by this website to this Agreement at any time.</p>
                    <p class="mb-3">1.3 You can become a member of this website (hereinafter referred to as "Member") by filling in the relevant information in accordance with the requirements of this website and successfully registering after other relevant procedures. Clicking the "Agree" button during the registration process means that you electronically Signed form of agreement with the company; or when you click on any button marked "Agree" or similar meaning during the use of this website, or actually use the services provided by this website in other ways permitted by this website, it means that you You fully understand, agree and accept to be bound by all the terms of this Agreement. The absence of your handwritten signature will not affect the legal binding force of this Agreement on you.</p>
                    <p class="mb-3">1.4 After becoming a member of this website, you will receive a member account and corresponding password. You are responsible for keeping the member account and password; you shall be legally responsible for all activities and events conducted under your account.</p>
                    <p class="mb-3">1.5 Only those who become members of this website can use the digital asset trading platform provided by this website to conduct transactions and enjoy other services stipulated by this website that are only available to members.</p>
                    <p class="mb-3">1.6 By registering and using any services and functions provided by this website, you will be deemed to have read, understood and:</p>
                    <p class="mb-3 ps-3">1.6.1 Accept to be bound by all terms and conditions of this Agreement.</p>
                    <p class="mb-3 ps-3">1.6.2 You confirm that you are over 16 years old or have the legal age to enter into a contract according to different applicable laws. Your registration on this website, selling or purchasing, publishing information, etc., accepting the services of this website It should comply with the relevant laws and regulations of the sovereign country or region that has jurisdiction over you, and have the full ability to accept these terms, enter into transactions, and use this website for digital asset transactions.</p>
                    <p class="mb-3 ps-3">1.6.3 You guarantee that all digital assets belonging to you involved in the transaction are legally obtained and have ownership.</p>
                    <p class="mb-3 ps-3">1.6.4 You agree that you are solely responsible for your own trading or non-trading activities and for any gains or losses.</p>
                    <p class="mb-3 ps-3">1.6.5 You confirm that the information provided when registering is true and accurate.</p>
                    <p class="mb-3 ps-3">1.6.6 You agree to comply with the requirements of any relevant law for tax purposes, including reporting any trading profits.</p>
                    <p class="mb-3 ps-3">1.6.7 You agree not to engage in or participate in behaviors or activities that harm the interests of this website or the company at any time, whether or not related to the services provided by this website.</p>
                    <p class="mb-3 ps-3">1.6.8 This agreement only governs the rights and obligations between you and us, and does not involve legal relationships and legal disputes arising from digital asset transactions between users of this website and other websites and you.</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">2. Agreement amendments</h5>
                <div class="ps-3">
                    <p>We reserve the right to amend this Agreement from time to time and announce it on the website without notifying you separately. The changed agreement will be marked with the change time on the homepage of this Agreement and will automatically take effect once it is announced on the website. You should browse and pay attention to the update time and updated content of this Agreement from time to time. If you do not agree with the relevant changes, you should immediately stop using the services of this website; your continued use of the services of this website means that you accept and agree to be bound by the revised agreement.</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">3. Registration</h5>
                <div class="ps-3">
                    <p class="mb-3">3.1 Registration qualifications</p>
                    <p class="mb-3 ps-3">Once you click the Agree to Register button, it means that you or your authorized agent has agreed to the content of the agreement and has been registered and used the services of this website by his agent. If you do not have the aforementioned subject qualifications, you and your authorized agent shall bear all consequences resulting therefrom, and the company reserves the right to cancel or permanently freeze your account, and hold you and your authorized agent accountable.</p>
                    
                    <p class="mb-3">3.2 Purpose of registration</p>
                    <p class="mb-3 ps-3">You confirm and promise that your registration on this website is not for the purpose of violating laws and regulations or disrupting the order of digital asset transactions on this website.</p>
                    
                    <p class="mb-3">3.3 Registration process</p>
                    <p class="mb-3 ps-3">3.3.1 You agree to provide a valid email address, mobile phone number and other information as required on the user registration page of this website. You can use the mobile phone number you provided as a login method to enter this website. All originally typed information will be referenced as registration information. You are responsible for the authenticity, completeness and accuracy of such information, and shall bear any direct or indirect losses and adverse consequences arising therefrom.</p>
                    <p class="mb-3 ps-3">3.3.2 If you provide the information required for registration legally, completely and effectively and have verified it, you are entitled to obtain the account number and password of this website. When you obtain the account number and password of this website. When you obtain the account number and password of this website, you are deemed to have successfully registered and can log in as a member on this website.</p>
                    <p class="mb-3 ps-3">3.3.3 You agree to receive emails and/or short messages sent by this website related to the management and operation of this website.</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">4. Service</h5>
                <div class="ps-3">
                    <p class="mb-3">4.1 Service content</p>
                    <p class="mb-3 ps-3">4.1.1 You have the right to browse the real-time market conditions and transaction information of various digital asset products on this website, and you have the right to submit digital asset transaction instructions and complete digital asset transactions through this website.</p>
                    <p class="mb-3 ps-3">4.1.2 You have the right to view the information under your membership account on this website and to apply the functions provided by this website to operate.</p>
                    <p class="mb-3 ps-3">4.1.3 You have the right to participate in website activities organized by this website in accordance with the activity rules published on this website.</p>
                    <p class="mb-3 ps-3">4.1.4 Other services this website promises to provide you.</p>
                    
                    <p class="mb-3">4.2. Service Rules</p>
                    <p class="mb-3">You promise to abide by the following service rules of this website:</p>
                    <p class="mb-3 ps-3">4.2.1 You should abide by laws and regulations and properly use and keep your account number, login password, fund password, and mobile phone verification code received on your mobile phone. You are fully responsible for any operations and consequences of using your account and login password, fund password, and mobile phone verification code.</p>
                    <p class="mb-3 ps-3">4.2.3 You agree to be responsible for all activities that occur under your account and password on this website (including but not limited to information disclosure, posting of information, online clicks to agree or submission of various rules and agreements, online renewal of agreements or purchase of services, etc.) responsibility.</p>
                    <p class="mb-3 ps-3">4.2.4 When conducting digital asset transactions on this website, you must not maliciously interfere with the normal progress of digital asset transactions or disrupt the order of transactions; you must not interfere with the normal operation of this website or interfere with other users' use of this website's services by any technical means or other means.; Do not maliciously defame the goodwill of this website by fabricating facts or other means.</p>
                    <p class="mb-3 ps-3">4.2.5 If you have a dispute with other users due to online transactions, you may not request this website to provide relevant information through non-judicial or administrative channels.</p>
                    <p class="mb-3 ps-3">4.2.6 All other expenses incurred during your use of the services provided by this website shall be solely judged and borne by you.</p>
                    <p class="mb-3 ps-3">4.2.7 Please do not operate the node service privately, it must be completed according to the platform regulations!</p>
                    <p class="mb-3 ps-3">4.2.8 If the user fails to conduct safe transactions according to the system node prompts, all consequences of privately operating the node shall be borne by the user.</p>
                    <p class="mb-3 ps-3">4.2.9 Users participating in Crypto Forest are not allowed to disclose any transaction information without authorization. Once a third party is found to have entered the platform through a link, the user will be immediately disqualified from participating.</p>
                    <p class="mb-3 ps-3">4.1.1.1 If two nodes are involved and an error occurs in any node, the transaction will fail, the account will be put into a silent state, and a repair node with a specific amount of money will need to be completed again.</p>
                    <p class="mb-3 ps-3">4.1.1.2 When participating in a trading node, a 1% fee will be charged for each order as the platform's service fee.</p>
                    
                    <p class="mb-3">4.3. Product Rules</p>
                    <p class="mb-3 ps-3">4.3.1 Browse transaction information</p>
                    <p class="mb-3 ps-3">When you browse transaction information on this website, you should carefully read all the contents contained in the transaction information, including but not limited to price, commission volume, handling fee, buying or selling direction, and you fully accept all contents contained in the transaction information. You can then click the button to proceed with the transaction.</p>
                    <p class="mb-3 ps-3">4.3.2 View transaction details</p>
                    <p class="mb-3 ps-3">You can view the corresponding transaction records through your account.</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">5. Rights and obligations of this website</h5>
                <div class="ps-3">
                    <p class="mb-3">5.1 If you do not have the registration qualifications stipulated in this agreement, this website has the right to refuse your registration. For those who have already registered, this website has the right to cancel your membership account. This website reserves the right to notify you or your authorized agent The right to accountability. At the same time, this website reserves the right to decide whether to accept your registration under any other circumstances.</p>
                    <p class="mb-3">5.2 Based on this website's own judgment, if this website finds that you or your associated account users are not suitable for high-risk investments, it has the right to suspend or terminate your account and the use of all associated accounts.</p>
                    <p class="mb-3">5.3 When this website discovers that the account user is not the initial registrant of the account, it has the right to suspend or terminate the use of the account.</p>
                    <p class="mb-3">5.4 When this website reasonably suspects that the information you provided is wrong, untrue, invalid or incomplete through technical testing, manual sampling and other testing methods, it has the right to notify you to correct or update the information or to suspend or terminate the provision of this website services to you.</p>
                    <p class="mb-3">5.5 This website has the right to correct any information displayed on this website when it is found that there are obvious errors.</p>
                    <p class="mb-3">5.6 This website reserves the right to modify, suspend or terminate the services of this website at any time. This website does not need to notify you in advance to exercise the right to modify or terminate services. If this website terminates one or more services of this website, the termination will start from this website in The termination announcement shall be effective on the date of publication on the website.</p>
                    <p class="mb-3">5.7 This website will adopt necessary technical means and management measures to ensure the normal operation of this website, provide necessary and reliable trading environment and trading services, and maintain the order of digital asset transactions.</p>
                    <p class="mb-3">5.8 If you do not log in to this website using your membership account and password for one consecutive year, this website has the right to cancel your account. After the account is canceled, this website has the right to open the corresponding member name to other users for registration and use.</p>
                    <p class="mb-3">5.9 This website ensures the security of your digital assets by strengthening technology investment, improving security precautions and other measures, and will notify you in advance when foreseeable security risks arise in your account.</p>
                    <p class="mb-3">5.10 This website has the right to delete all kinds of content information on this website that does not comply with laws and regulations or the regulations of this website at any time. This website does not need to notify you in advance to exercise such rights.</p>
                    <p class="mb-3">5.11 This website has the right to request you to provide more information or materials in accordance with the laws, regulations, rules, orders and other regulatory requirements of your sovereign country or region, and to take reasonable measures to comply with local regulatory requirements., you are obliged to cooperate; this website has the right to suspend or permanently stop opening some or all of this website's services to you in accordance with the requirements of the laws, regulations, rules, orders and other specifications of your sovereign country or region.</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">6. Limitation of liability and disclaimer</h5>
                <div class="ps-3">
                    <p class="mb-3">6.1 You understand and agree that under any circumstances, we will not be responsible for the following matters:</p>
                    <p class="mb-3 ps-3">6.1.1 Loss of income;</p>
                    <p class="mb-3 ps-3">6.1.2 Trading profits or contract losses;</p>
                    <p class="mb-3 ps-3">6.1.3 Losses caused by business interruption;</p>
                    <p class="mb-3 ps-3">6.1.4 Loss of expected monetary savings;</p>
                    <p class="mb-3 ps-3">6.1.5 Losses caused by information problems;</p>
                    <p class="mb-3 ps-3">6.1.6 Loss of opportunity, goodwill or reputation;</p>
                    <p class="mb-3 ps-3">6.1.7 Damage or loss of data;</p>
                    <p class="mb-3 ps-3">6.1.8 The cost of purchasing substitute products or services;</p>
                    <p class="mb-3 ps-3">6.1.9 Any indirect, special or incidental loss or damage arising from tort (including negligence), breach of contract or any other reason, whether or not such loss or damage can be reasonably foreseen by us; whether or not we have been informed in advance Be advised of the possibility of such loss or damage.</p>
                    <p class="mb-3">Clauses 6.1.1 to 8.1.9 are independent of each other.</p>
                    
                    <p class="mb-3">6.2 You understand and agree that we are not responsible for any damages caused to you due to any of the following circumstances:</p>
                    <p class="mb-3 ps-3">6.2.1 There may be major violations of laws or breaches of contract in your specific transactions.</p>
                    <p class="mb-3 ps-3">6.2.2 Your behavior on this website is suspected of being illegal or immoral.</p>
                    <p class="mb-3 ps-3">6.2.3 Expenses and losses incurred by purchasing or obtaining any data, information or conducting transactions through the services of this website or by substitute actions.</p>
                    <p class="mb-3 ps-3">6.2.4 Your misunderstanding of the services of this website.</p>
                    <p class="mb-3 ps-3">6.2.5 Any other losses related to the services provided by this website that are not caused by us.</p>
                    
                    <p class="mb-3">6.3 We are responsible for any loss due to information network equipment maintenance, information network connection failure, computer, communication or other system failure, power failure, weather conditions, accidents, strikes, labor disputes, riots, uprisings, riots, insufficient productivity or production means, Fire, flood, storm, explosion, war, bank or other partner reasons, digital asset market collapse, government actions, orders of judicial or administrative agencies, other actions beyond our control or that we have no ability to control, or actions of third parties We do not assume any responsibility for any inability or delay in service, or any losses caused to you.</p>
                    
                    <p class="mb-3">6.4 We cannot guarantee that all the information, programs, texts, etc. contained in this website are completely safe and free from interference and damage by any viruses, Trojans and other malicious programs. Therefore, you can log in and use any services on this website or download and use any downloaded content. Programs, information, data, etc. are all your personal decisions and you bear the risks and possible losses at your own risk.</p>
                    
                    <p class="mb-3">6.5 We do not make any guarantees or commitments about any information, products and services of any third-party websites linked to this website or any other content that does not belong to our subject. If you use any services provided by third-party websites, Information and products are your own decisions and you bear all responsibilities arising therefrom.</p>
                    
                    <p class="mb-3">6.6 We do not make any express or implied guarantees for your use of the services on this website, including but not limited to the suitability of the services provided by this website, no errors or omissions, continuity, accuracy, reliability, and suitability for a particular purpose.. At the same time, we do not make any commitment or guarantee as to the validity, accuracy, correctness, reliability, quality, stability, completeness and timeliness of the technology and information involved in the services provided by this website. Whether to log in or use the services provided by this website is your personal decision and you bear your own risks and possible losses. We do not make any express or implied guarantees regarding the market, value and price of digital assets. You understand and understand that the digital asset market is unstable. Prices and values may fluctuate or collapse significantly at any time. Trading digital assets is your personal freedom. Choose and decide at your own risk and possible losses.</p>
                    
                    <p class="mb-3">6.7 Our warranties and commitments set out in this Agreement are the only warranties and representations made by us in relation to this Agreement and the services provided by this website, and supersede any warranties and commitments arising in any other way and manner, whether written or oral., express or implied. All these warranties and representations only represent our own commitments and guarantees and do not guarantee that any third party will comply with the guarantees and commitments in this Agreement.</p>
                    
                    <p class="mb-3">6.8 We do not waive any rights we have not mentioned in this Agreement to limit, exclude or set off our liability for damages to the fullest extent applicable law.</p>
                    
                    <p class="mb-3">6.9 After you register, you agree to any operations we perform in accordance with the rules stipulated in this agreement, and any risks incurred will be borne by you.</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">7. Termination of Agreement</h5>
                <div class="ps-3">
                    <p class="mb-3">7.1 This website has the right to terminate all services of this website in accordance with the provisions of this agreement. This agreement will be terminated on the date of termination of all services of this website.</p>
                    <p class="mb-3">7.2 After the termination of this Agreement, you have no right to require this website to continue to provide it with any services or perform any other obligations,including but not limited to requiring this website to retain or disclose to you any information in its original website account to you. Or a third party forwards any information that it has not read or sent.</p>
                    <p class="mb-3">7.3 The termination of this agreement will not affect the observant party's requirement to assume other responsibilities from the breaching party.</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">8. Intellectual property rights</h5>
                <div class="ps-3">
                    <p class="mb-3">8.1 All intellectual achievements contained in this website include but are not limited to website logos, databases, website designs, text and graphics, software, photos, videos, music, sounds and combinations of the foregoing, software compilations, related source codes and software (including The intellectual property rights of applets and scripts) belong to this website. You may not reproduce, alter, copy, send or use any of the foregoing materials or content for commercial purposes.</p>
                    <p class="mb-3">8.2 All rights contained in the name of this website (including but not limited to goodwill and trademarks, logos) belong to the company.</p>
                    <p class="mb-3">8.3 You shall not illegally use or dispose of the intellectual property rights of this website or others when using the services of this website. You may not publish or authorize other websites (and media) to use the information published on this website in any form.</p>
                    <p class="mb-3">8.4 Your logging into this website or using any services provided by this website will not be deemed as our transfer of any intellectual property rights to you.</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">9. Calculation</h5>
                <div class="ps-3">
                    <p>All transaction calculations are verified by us, but we cannot guarantee that use of the website will be uninterrupted or error-free.</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">10. Divisibility</h5>
                <div class="ps-3">
                    <p>If any provision of this Agreement is held to be unenforceable, invalid or illegal by any court of competent jurisdiction, it will not affect the validity of the remaining provisions of this Agreement.</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">11. Non-agency relationship</h5>
                <div class="ps-3">
                    <p>Nothing in this Agreement shall be deemed to create, imply or otherwise constitute us as your agent, trustee or other representative, except as otherwise provided in this Agreement.</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">12. Abstention</h5>
                <div class="ps-3">
                    <p>The waiver by us or you of any party's liability for breach of contract or other liability stipulated in this Agreement shall not be deemed or interpreted as a waiver of other liability for breach of contract; the failure to exercise any right or remedy shall not in any way be construed as a waiver of such rights or remedies. of giving up.</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">13. Title</h5>
                <div class="ps-3">
                    <p>All headings are for convenience of expression of the agreement only and are not used to expand or limit the content or scope of the terms of the agreement. Deposit agreement for others: Deposit USDT, ETH, BTC, and multi-currency transactions for others. Once detected, strict penalties will be imposed!</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">14. Effectiveness and interpretation of the agreement</h5>
                <div class="ps-3">
                    <p class="mb-3">14.1 This agreement takes effect when you click on the registration page of this website to agree to register, complete the registration process, and obtain an account and password for this website, and is binding on both this website and you.</p>
                    <p>14.2 The final interpretation right of this agreement belongs to this website.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
    }
    ol {
        padding-left: 1.2rem;
    }
    ol li {
        color: var(--text-muted);
    }
    ol li::marker {
        color: var(--bs-primary);
        font-weight: 600;
    }
</style>
@endpush
@endsection