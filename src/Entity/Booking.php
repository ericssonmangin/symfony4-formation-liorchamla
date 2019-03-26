<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Votre date doit être au bon format")
     * @Assert\GreaterThan("today", message="La date d'arrivée doit être supérieure à la date du jour", groups={"Frontend"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Votre date doit être au bon format")
     * @Assert\GreaterThan(propertyPath="startDate", message="Date de départ choisie inférieure à la date d'arrivée")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @throws \Exception
     */
    public function prePersist()
    {
        if(empty($this->createdAt)){
            $this->createdAt = new DateTime();
        }
        if(empty($this->amount)){
            /** @noinspection PhpUndefinedMethodInspection */
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    public function getDuration(){
        /** @noinspection PhpUndefinedMethodInspection */
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
    }

    /**
     * Check Reservation Possible
     */
    public function isBookableDates(){
        // Recup Tableau des jours occupés + Tableau des jours dans reservation
        /** @noinspection PhpUndefinedMethodInspection */
        $notAvailableDays = $this->ad->getNotAvailableDays();
        $bookingDays = $this->getDays();

        // Formattage Jours
        $formatDay = function($day){
            /** @var DateTime $day */
            return $day->format('Y-m-d');
        };
        $days = array_map($formatDay, $bookingDays);
        $notAvailable = array_map($formatDay, $notAvailableDays);

        // Comparaison jours dans reservation et jours occupés
        foreach($days as $day){
            if(array_search($day, $notAvailable) !== false) return false;
        }

        return true;
    }

    /**
     * Jours dans reservation
     * @return array
     */
    public function getDays(){
        /** @noinspection PhpUndefinedMethodInspection */
        $result = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24 * 60 * 60
        );

        $days = array_map(function($dayTimestamp){
            return new DateTime(date('Y-m-d', $dayTimestamp));
        }, $result);

        return $days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

}
