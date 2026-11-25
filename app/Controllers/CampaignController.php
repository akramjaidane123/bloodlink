<?php
class CampaignController extends Controller
{
    protected string $page  = 'campaigns';
    protected string $title = 'Campagnes de collecte | BloodLink';

    public function index(): void
    {
        $this->render('campaigns', ['campaigns' => Campaign::upcoming()]);
    }
}
