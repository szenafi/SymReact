<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['customers_read']],
    
    )]
#[ApiFilter(SearchFilter::class, properties: ['firstName','lastName', 'company', 'email', 'user'])]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["customers_read", "invoices_read"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["customers_read", "invoices_read"])]
    #[Assert\NotBlank(message:"Le prénom est obligatoire")]
    #[Assert\Length(min:3, max:255, minMessage:"Le prénom doit faire au moins 3 caractères", maxMessage:"Le prénom doit faire au plus 255 caractères")]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["customers_read", "invoices_read"])]
    #[Assert\NotBlank(message:"Le nom est obligatoire")]
    #[Assert\Length(min:3, max:255, minMessage:"Le nom doit faire au moins 3 caractères", maxMessage:"Le nom doit faire au plus 255 caractères")]
    private $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["customers_read", "invoices_read"])]
    #[Assert\NotBlank(message:"L'email est obligatoire")]
    #[Assert\Email(message:"Cet email n'est pas valide")]

    private $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(["customers_read", "invoices_read"])]
    private $company;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Invoice::class)]
    #[Groups(["customers_read"])]

    private $invoices;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'customers')]
    #[Groups(["customers_read", "invoices_read"])]
    #[Assert\NotBlank(message:"L'utilisateur est obligatoire")]
    private $user;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }
    
    # Permet de récupérer le total des invoices d'un customer
    #[Groups(["customers_read"])]
    #return float
    public function getTotalAmount(): float
    {
        return array_reduce(
            $this->invoices->toArray(),
            function ($total, $invoice) {
                return $total + $invoice->getAmount();
            },
            0
        );
    }


    # Récupèrer le montant total non payé (montant total hors factures payées)
    #[Groups(["customers_read"])]
    #return float
    public function getUnpaidAmount(): float
    {
        return array_reduce(
            $this->invoices->toArray(),
            function ($total, $invoice) {
                return $total + ($invoice->getStatus() === 'PAID' ? 0 : $invoice->getAmount());
            },
            0
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setCustomer($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
